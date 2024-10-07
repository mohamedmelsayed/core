<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\GeneralSetting;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class VideoUploader
{
    private $general;
    public $oldServer;
    public $oldFile;
    public $fileName;
    public $uploadedServer;
    public $error;

    public function __construct()
    {
        $this->general = gs();
    }

    public function upload()
    {
        $uploadDisk = $this->general->server;
        try {
            switch ($uploadDisk) {
                case Status::CURRENT_SERVER:
                    $this->uploadedServer = Status::CURRENT_SERVER;
                    $this->uploadToCurrentServer();
                    break;
                case Status::FTP_SERVER:
                    $this->uploadedServer = Status::FTP_SERVER;
                    $this->configureFTP();
                    $this->uploadToServer('custom-ftp', 'videos');
                    break;
                case Status::WASABI_SERVER:
                    $this->uploadedServer = Status::WASABI_SERVER;
                    $this->configureDisk('wasabi');
                    $this->uploadToServer('wasabi', 'videos');
                    break;
                case Status::DIGITAL_OCEAN_SERVER:
                    $this->uploadedServer = Status::DIGITAL_OCEAN_SERVER;
                    $this->uploadToServer('digital_ocean', 'videos');
                    break;
                case Status::AWS_CDN:
                    $this->uploadedServer = Status::AWS_CDN;
                    $this->uploadToAWSCDN();
                    break;
                default:
                    throw new \Exception("Invalid upload disk: $uploadDisk");
            }
        } catch (\Exception $e) {
            $this->error = true;
            Log::error('Error during upload process: ' . $e->getMessage());
        }
    }

    private function uploadToCurrentServer()
    {
        try {
            $date = date('Y/m/d');
            $file = $this->file;
            $path = "assets/videos/$date";
            $this->fileName = $date . '/' . fileUploader($file, $path, null);
        } catch (\Exception $e) {
            $this->error = true;
            Log::error('Error uploading to current server: ' . $e->getMessage());
        }
    }

    private function uploadToServer($server, $param)
    {
        $date = date('Y/m/d');
        $file = $this->file;
        $path = "$param/$date";
        $fileExtension = $file->getClientOriginalExtension();
        $video = uniqid() . time() . '.' . $fileExtension;

        try {
            $fileContents = file_get_contents($file);
            $disk = Storage::disk($server);
            $this->makeDirectory($path, $disk);
            $disk->put("$path/$video", $fileContents);
            $this->fileName = "$path/$video";
        } catch (Exception $ex) {
            $this->error = true;
            Log::error("Error uploading to server $server: " . $ex->getMessage());
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

    public function configureFTP()
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
        } catch (\Exception $e) {
            $this->error = true;
            Log::error('Error configuring FTP: ' . $e->getMessage());
        }
    }

    public function configureDisk($server)
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
        } catch (\Exception $e) {
            $this->error = true;
            Log::error('Error configuring disk: ' . $e->getMessage());
        }
    }

    public function removeFtpVideo()
    {
        try {
            $oldFile = $this->oldFile;
            $storage = Storage::disk('custom-ftp');

            if ($storage->exists($oldFile)) {
                $storage->delete($oldFile);
            }
        } catch (\Exception $e) {
            Log::error('Error removing FTP video: ' . $e->getMessage());
        }
    }

    public function removeOldFile()
    {
        try {
            if ($this->oldServer == Status::AWS_CDN) {
                $this->removeFromAWSCDN($this->oldFile);
            } else if ($this->oldServer == Status::CURRENT_SERVER) {
                $location = "assets/videos/{$this->oldFile}";
                fileManager()->removeFile($location);
            } else if (in_array($this->oldServer, [Status::FTP_SERVER, Status::WASABI_SERVER, Status::DIGITAL_OCEAN_SERVER])) {
                $server = $this->getServerName($this->oldServer);
                $this->configureDisk($server);
                $disk = Storage::disk($server);
                if ($disk->exists($this->oldFile)) {
                    $disk->delete($this->oldFile);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error removing old file: ' . $e->getMessage());
        }
    }

    private function getServerName($serverStatus)
    {
        return match ($serverStatus) {
            Status::FTP_SERVER => 'custom-ftp',
            Status::WASABI_SERVER => 'wasabi',
            Status::DIGITAL_OCEAN_SERVER => 'digital_ocean',
            default => throw new \Exception("Unknown server status: $serverStatus"),
        };
    }

    private function uploadToAWSCDN()
    {
        $date = date('Y/m/d');
        $file = $this->file;
        $path = "videos/$date";
        $fileName = $file->getClientOriginalName();

        try {
            $s3 = $this->initializeS3Client();
            $s3->putObject([
                'Bucket' => $this->general->aws->bucket,
                'Key' => "$path/$fileName",
                'Body' => file_get_contents($file),
                'ACL' => 'public-read',
            ]);
            $this->fileName = "$path/$fileName";
        } catch (S3Exception $e) {
            $this->error = true;
            Log::error('AWS S3 upload error: ' . $e->getMessage());
        }
    }

    private function removeFromAWSCDN($oldFile)
    {
        try {
            $s3 = $this->initializeS3Client();
            $s3->deleteObject([
                'Bucket' => $this->general->aws->bucket,
                'Key' => $oldFile,
            ]);
        } catch (S3Exception $e) {
            Log::error('AWS S3 delete error: ' . $e->getMessage());
        }
    }

    private function initializeS3Client()
    {
        return new S3Client([
            'version' => 'latest',
            'region' => $this->general->aws->region,
            'credentials' => [
                'key' => $this->general->aws->key,
                'secret' => $this->general->aws->secret,
            ],
        ]);
    }
}
