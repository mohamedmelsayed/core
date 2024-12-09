<?php

namespace App\Lib;

use App\Constants\Status;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class VideoUploader
{
    private $general;
    public $oldServer;
    public $oldFile;
    public $file;
    public $fileName;
    public $uploadedServer;
    public $error;

    public function __construct()
    {
        $this->general = gs();
    }

    public function upload()
    {
        $this->uploadedServer = $this->general->server;

        try {
            switch ($this->uploadedServer) {
                case 'current':
                    $this->uploadedServer = Status::CURRENT_SERVER;
                    $this->uploadToCurrentServer();
                    break;
                case 'custom-ftp':
                    $this->uploadedServer = Status::FTP_SERVER;
                    $this->configureFTP();
                    $this->uploadToServer('custom-ftp', 'videos');
                    break;
                case 'wasabi':
                    $this->uploadedServer = Status::WASABI_SERVER;
                    $this->configureDisk('wasabi');
                    $this->uploadToServer('wasabi', 'videos');
                    break;
                case 'digital_ocean':
                    $this->uploadedServer = Status::DIGITAL_OCEAN_SERVER;
                    $this->uploadToServer('digital_ocean', 'videos');
                    break;
                case 'aws':
                    $this->initializeS3Client();
                    $this->uploadedServer = Status::AWS_CDN;
                    $this->uploadToAwsS3();
                    break;
                default:
                    throw new Exception("Invalid upload disk: $this->uploadedServer");
            }
        } catch (Exception $e) {
            $this->error = true;
            Log::error('Error during upload process: ' . $e->getMessage());
        }
    }

    private function uploadToCurrentServer()
    {
        try {
            $date = date('Y/m/d');
            $path = "assets/videos/$date";
            $this->fileName = $date . '/' . fileUploader($this->file, $path, null);
        } catch (Exception $e) {
            $this->error = true;
            Log::error('Error uploading to current server: ' . $e->getMessage());
        }
    }

    private function uploadToServer($server, $param)
    {
        try {
            $date = date('Y/m/d');
            $path = "$param/$date";
            $fileName = uniqid() . time() . '.' . $this->file->getClientOriginalExtension();

            $fileContents = file_get_contents($this->file);
            $disk = Storage::disk($server);

            $this->makeDirectory($path, $disk);
            $disk->put("$path/$fileName", $fileContents);
            $this->fileName = "$path/$fileName";
        } catch (Exception $e) {
            $this->error = true;
            Log::error("Error uploading to server $server: " . $e->getMessage());
        }
    }

    private function uploadToAwsS3()
    {
        try {
            $date = date('Y/m/d');
            $path = "assets/videos/$date";
            $fileName = uniqid() . '_' . $this->file->getClientOriginalName();

            $s3Path = Storage::disk('s3')->putFileAs($path, $this->file, $fileName, 'public');

            if ($s3Path) {
                $this->fileName = $s3Path;
                Log::info("File successfully uploaded to S3: $s3Path");
            } else {
                throw new Exception("Failed to upload file to S3.");
            }
        } catch (Exception $e) {
            $this->error = true;
            Log::error("Error uploading to AWS S3: " . $e->getMessage());
        }
    }

    private function removeFromAWSCDN($oldFile)
    {
        try {
            if (Storage::disk('s3')->exists($oldFile)) {
                Storage::disk('s3')->delete($oldFile);
                Log::info("File successfully removed from S3: $oldFile");
            } else {
                Log::warning("File not found on S3: $oldFile");
            }
        } catch (Exception $e) {
            $this->error = true;
            Log::error("Error removing file from AWS S3: " . $e->getMessage());
        }
    }

    private function makeDirectory($path, $disk)
    {
        try {
            if (!$disk->exists($path)) {
                $disk->makeDirectory($path);
            }
        } catch (Exception $e) {
            Log::error('Error creating directory: ' . $e->getMessage());
        }
    }

    private function configureFTP()
    {
        try {
            Config::set('filesystems.disks.custom-ftp', [
                'driver' => 'ftp',
                'host' => $this->general->ftp->host,
                'username' => $this->general->ftp->username,
                'password' => $this->general->ftp->password,
                'port' => 21,
                'root' => $this->general->ftp->root,
            ]);
        } catch (Exception $e) {
            $this->error = true;
            Log::error('Error configuring FTP: ' . $e->getMessage());
        }
    }

    private function configureDisk($server)
    {
        try {
            Config::set("filesystems.disks.$server", [
                'visibility' => 'public',
                'driver' => $this->general->$server->driver,
                'key' => $this->general->$server->key,
                'secret' => $this->general->$server->secret,
                'region' => $this->general->$server->region,
                'bucket' => $this->general->$server->bucket,
                'endpoint' => $this->general->$server->endpoint,
            ]);
        } catch (Exception $e) {
            $this->error = true;
            Log::error('Error configuring disk: ' . $e->getMessage());
        }
    }

    public function removeOldFile()
    {
        try {
            if ($this->oldServer == Status::AWS_CDN) {
                $this->removeFromAWSCDN($this->oldFile);
            } elseif ($this->oldServer == Status::CURRENT_SERVER) {
                $location = "assets/videos/{$this->oldFile}";
                fileManager()->removeFile($location);
            } else {
                $server = $this->getServerName($this->oldServer);
                $this->configureDisk($server);
                $disk = Storage::disk($server);
                if ($disk->exists($this->oldFile)) {
                    $disk->delete($this->oldFile);
                }
            }
        } catch (Exception $e) {
            Log::error('Error removing old file: ' . $e->getMessage());
        }
    }

    private function getServerName($serverStatus)
    {
        return match ($serverStatus) {
            Status::FTP_SERVER => 'custom-ftp',
            Status::WASABI_SERVER => 'wasabi',
            Status::DIGITAL_OCEAN_SERVER => 'digital_ocean',
            default => throw new Exception("Unknown server status: $serverStatus"),
        };
    }

    private function initializeS3Client()
    {
        try {
            $awsCdnConfig = json_decode($this->general->aws_cdn, true);

            if (is_null($awsCdnConfig)) {
                throw new Exception("Failed to decode aws_cdn configuration.");
            }

            Config::set("filesystems.disks.s3", [
                'visibility' => 'public',
                'driver' => 's3',
                'key' => $awsCdnConfig['access_key'] ?? '',
                'secret' => $awsCdnConfig['secret_key'] ?? '',
                'region' => $awsCdnConfig['region'] ?? '',
                'bucket' => $awsCdnConfig['bucket'] ?? '',
                'endpoint' => $awsCdnConfig['endpoint'] ?? null,
            ]);
        } catch (Exception $e) {
            $this->error = true;
            Log::error("Failed to initialize S3 client: " . $e->getMessage());
        }
    }
}