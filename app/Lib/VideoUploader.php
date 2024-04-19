<?php

namespace App\Lib;

use App\Constants\Status;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class VideoUploader {
    private $general;

    public $fileName;
    public $error;

    public function __construct() {
        $this->general = gs();
    }

    public function upload() {
        $uploadDisk = $this->general->server;

        try {
            switch ($uploadDisk) {
                case Status::CURRENT_SERVER:
                    $this->uploadToCurrentServer();
                    break;
                case Status::FTP_SERVER:
                    $this->uploadToServer('custom-ftp', 'videos');
                    break;
                case Status::WASABI_SERVER:
                    $this->uploadToServer('wasabi', 'videos');
                    break;
                case Status::DIGITAL_OCEAN_SERVER:
                    $this->uploadToServer('digital_ocean', 'videos');
                    break;
                default:
                    throw new \Exception("Invalid upload disk: $uploadDisk");
            }
        } catch (\Exception $e) {
            $this->error = true;
        }
    }

    private function uploadToCurrentServer() {
        $date = date('Y/m/d');
        $file = $this->file;
        $path = "assets/videos/$date";

        $this->fileName = $date . '/' . fileUploader($file, $path, null);
    }

    private function uploadToServer($server, $param) {
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

    private function makeDirectory($path, $disk) {
        if (!$disk->exists($path)) {
            $disk->makeDirectory($path);
        }
    }

    public function configureFTP() {
        $general = $this->general;

        Config::set('filesystems.disks.custom-ftp.driver', 'ftp');
        Config::set('filesystems.disks.custom-ftp.host', $general->ftp->host);
        Config::set('filesystems.disks.custom-ftp.username', $general->ftp->username);
        Config::set('filesystems.disks.custom-ftp.password', $general->ftp->password);
        Config::set('filesystems.disks.custom-ftp.port', 21);
        Config::set('filesystems.disks.custom-ftp.root', $general->ftp->root);
    }

    public function configureDisk($server) {
        $general = $this->general;

        Config::set("filesystems.disks.$server.visibility", 'public');
        Config::set("filesystems.disks.$server.driver", $general->$server->driver);
        Config::set("filesystems.disks.$server.key", $general->$server->key);
        Config::set("filesystems.disks.$server.secret", $general->$server->secret);
        Config::set("filesystems.disks.$server.region", $general->$server->region);
        Config::set("filesystems.disks.$server.bucket", $general->$server->bucket);
        Config::set("filesystems.disks.$server.endpoint", $general->$server->endpoint);
    }

    public function removeFtpVideo() {
        $oldFile = $this->oldFile;
        $storage = Storage::disk('custom-ftp');

        if ($storage->exists($oldFile)) {
            $storage->delete($oldFile);
        }
    }

    public function removeOldFile() {
        if ($this->oldServer == Status::CURRENT_SERVER) {
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
}
