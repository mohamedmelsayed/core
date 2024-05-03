<?php

namespace App\Lib;

use App\Constants\Status;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class VideoUploader
{
    private $general;
    public $oldServer;
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
                    $this->uploadToServer('wasabi', 'videos');
                    break;
                case Status::DIGITAL_OCEAN_SERVER:
                    $this->uploadedServer = Status::DIGITAL_OCEAN_SERVER;
                    $this->uploadToServer('digital_ocean', 'videos');
                    break;
                case Status::AWS_CDN: // New case for AWS CDN
                    $this->uploadedServer = Status::AWS_CDN;
                    $this->uploadToAWSCDN(); // Call the new method to upload to AWS CDN
                    break;
                default:
                    throw new \Exception("Invalid upload disk: $uploadDisk");
            }
        } catch (\Exception $e) {
            $this->error = true;
        }
    }

    private function uploadToCurrentServer()
    {
        $date = date('Y/m/d');
        $file = $this->file;
        $path = "assets/videos/$date";

        $this->fileName = $date . '/' . fileUploader($file, $path, null);
    }

    private function uploadToServer($server, $param)
    {
        $date = date('Y/m/d');
        $file = $this->file;
        $path = "$param/$date";

        $fileExtension = $file->getClientOriginalExtension();
        $fileContents = file_get_contents($file);
        $disk = Storage::disk($server);

        $this->makeDirectory($path, $disk);

        $video = uniqid() . time() . '.' . $fileExtension;

        $disk->put("$path/$video", $fileContents);

        $this->fileName = "$path/$video";
    }

    private function makeDirectory($path, $disk)
    {
        if (!$disk->exists($path)) {
            $disk->makeDirectory($path);
        }
    }
    public function configureFTP() {
        $general = $this->general;
        //ftp
        try {
        Config::set('filesystems.disks.custom-ftp.driver', 'ftp');
        Config::set('filesystems.disks.custom-ftp.host', $general->ftp->host);
        Config::set('filesystems.disks.custom-ftp.username', $general->ftp->username);
        Config::set('filesystems.disks.custom-ftp.password', $general->ftp->password);
        Config::set('filesystems.disks.custom-ftp.port', 21);
        Config::set('filesystems.disks.custom-ftp.root', $general->ftp->root);
    } catch (\Exception $e) {
        // Handle the error (e.g., log or display an error message)
        // You can log the exception message for debugging purposes
        Log::error('Error setting filesystem configuration: ' . $e->getMessage());
    }

    }
    public function configureDisk($server) {
        $general = $this->general;
        try {
            Config::set('filesystems.disks.' . $server . '.visibility', 'public');
            Config::set('filesystems.disks.' . $server . '.driver', $general->$server->driver);
            Config::set('filesystems.disks.' . $server . '.key', $general->$server->key);
            Config::set('filesystems.disks.' . $server . '.secret', $general->$server->secret);
            Config::set('filesystems.disks.' . $server . '.region', $general->$server->region);
            Config::set('filesystems.disks.' . $server . '.bucket', $general->$server->bucket);
            Config::set('filesystems.disks.' . $server . '.endpoint', $general->$server->endpoint);
        } catch (\Exception $e) {
            // Handle the error (e.g., log or display an error message)
            // You can log the exception message for debugging purposes
            Log::error('Error setting filesystem configuration: ' . $e->getMessage());
        }
    }

    public function removeFtpVideo()
    {
        $oldFile = $this->oldFile;
        $storage = Storage::disk('custom-ftp');

        if ($storage->exists($oldFile)) {
            $storage->delete($oldFile);
        }
    }

    public function removeOldFile()
    {

        if ($this->oldServer == Status::AWS_CDN) {
            $this->removeFromAWSCDN($this->oldFile); // Call the new method to remove from AWS CDN
        } else if ($this->oldServer == Status::CURRENT_SERVER) {
            $location = "assets/videos/{$this->oldFile}";
            fileManager()->removeFile($location);
        } else if (in_array($this->oldServer, [Status::FTP_SERVER, Status::WASABI_SERVER, Status::DIGITAL_OCEAN_SERVER])) {
            try {
                if ($this->oldServer == Status::WASABI_SERVER) {
                    $server = 'wasabi';
                } else if ($this->oldServer == Status::DIGITAL_OCEAN_SERVER) {
                    $server = 'digital_ocean';
                } else if ($this->oldServer == Status::FTP_SERVER) {
                    $server = 'custom-ftp';
                }

                $this->configureDisk($server);
                $disk = Storage::disk($server);
                $disk->delete($this->oldFile);
            } catch (\Exception $e) {
            }
        }
    }


    private function uploadToAWSCDN()
    {
        $date = date('Y/m/d');
        $file = $this->file;
        $path = "videos/$date";

        $fileExtension = $file->getClientOriginalExtension();
        $fileContents = file_get_contents($file);

        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->general->aws->region,
            'credentials' => [
                'key'    => $this->general->aws->key,
                'secret' => $this->general->aws->secret,
            ],
        ]);

        try {
            $s3->putObject([
                'Bucket' => $this->general->aws->bucket,
                'Key'    => "$path/{$file->getClientOriginalName()}",
                'Body'   => $fileContents,
                'ACL'    => 'public-read',
            ]);

            $this->fileName = "$path/{$file->getClientOriginalName()}";
        } catch (S3Exception $e) {
            $this->error = true;
        }
    }
    private function removeFromAWSCDN($oldFile)
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region'  => $this->general->aws->region,
            'credentials' => [
                'key'    => $this->general->aws->key,
                'secret' => $this->general->aws->secret,
            ],
        ]);

        try {
            $s3->deleteObject([
                'Bucket' => $this->general->aws->bucket,
                'Key'    => $oldFile,
            ]);
        } catch (S3Exception $e) {
            // Handle deletion error
        }
    }
}
