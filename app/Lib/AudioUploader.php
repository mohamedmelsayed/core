<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AudioUploader
{
    private $general;
    private $date;
    public $path;
    public $file;
    public $oldFile;
    public $oldServer;
    public $uploadedServer;
    public $fileName;
    public $error;

    public function __construct()
    {
        $this->general = GeneralSetting::first();
        $this->date = date('Y/m/d'); // Simplified date creation
    }

    public function upload()
    {
        if ($this->oldFile) {
            $this->removeOldFile();
        }

        $uploadDisk = $this->general->server;

        switch ($uploadDisk) {
            case Status::CURRENT_SERVER:
                $this->uploadedServer = Status::CURRENT_SERVER;
                $this->uploadToCurrentServer();
                break;
            case Status::FTP_SERVER:
                $this->uploadedServer = Status::FTP_SERVER;
                $this->uploadToFtpServer();
                break;
            default:
                $this->error = true;
                Log::error("Unsupported upload server: $uploadDisk");
        }
    }

    private function uploadToCurrentServer()
    {
        try {
            $location = 'assets/audios/';
            $path = $location . $this->date;
            $this->fileName = $this->date . '/' . fileUploader($this->file, $path, null);
        } catch (\Exception $exp) {
            $this->error = true;
            Log::error('Error uploading to the current server: ' . $exp->getMessage());
        }
    }

    private function uploadToFtpServer()
    {
        try {
            $this->configureDisk();
            $fileExtension = $this->file->getClientOriginalExtension();
            $disk = Storage::disk('custom-ftp');
            $location = 'audios/';
            $path = $location . $this->date;

            $this->makeDirectoryIfNotExists($path, $disk);

            $uniqueFileName = uniqid() . time() . '.' . $fileExtension;
            $disk->put($path . '/' . $uniqueFileName, File::get($this->file));
            $this->fileName = $path . '/' . $uniqueFileName;
        } catch (\Exception $e) {
            $this->error = true;
            Log::error('Error uploading to FTP server: ' . $e->getMessage());
        }
    }

    private function makeDirectoryIfNotExists($path, $disk)
    {
        try {
            if (!$disk->exists($path)) {
                $disk->makeDirectory($path);
            }
        } catch (\Exception $e) {
            Log::error('Error creating directory on FTP server: ' . $e->getMessage());
        }
    }

    public function configureDisk()
    {
        try {
            $ftpConfig = [
                'driver' => 'ftp',
                'host' => $this->general->ftp->host,
                'username' => $this->general->ftp->username,
                'password' => $this->general->ftp->password,
                'port' => 21,
                'root' => $this->general->ftp->root,
            ];
            Config::set('filesystems.disks.custom-ftp', $ftpConfig);
        } catch (\Exception $e) {
            $this->error = true;
            Log::error('Error configuring FTP disk: ' . $e->getMessage());
        }
    }

    public function removeFtpVideo()
    {
        try {
            $storage = Storage::disk('custom-ftp');
            if ($storage->exists($this->oldFile)) {
                $storage->delete($this->oldFile);
            }
        } catch (\Exception $e) {
            Log::error('Error removing FTP video: ' . $e->getMessage());
        }
    }

    public function removeOldFile()
    {
        try {
            if ($this->oldServer == Status::CURRENT_SERVER) {
                $location = 'assets/audios/' . $this->oldFile;
                fileManager()->removeFile($location);
            } elseif ($this->oldServer == Status::FTP_SERVER) {
                $this->configureDisk();
                $disk = Storage::disk('custom-ftp');
                if ($disk->exists($this->oldFile)) {
                    $disk->delete($this->oldFile);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error removing old file: ' . $e->getMessage());
        }
    }
}
