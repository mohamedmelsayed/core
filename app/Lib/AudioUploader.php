<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AudioUploader
{
    private $general;

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
        $date = date('Y').'/'.date('m').'/'.date('d');
        $this->date = $date;
    }

    public function upload()
    {
        $general = $this->general;

        $uploadDisk = $general->server;
        if($this->oldFile){
            $this->removeOldFile();
        }

        if ($uploadDisk == Status::CURRENT_SERVER) {
            $this->uploadedServer = 0;
            return $this->uploadToCurrentServer();
        }
        if ($uploadDisk == Status::FTP_SERVER) {
            $this->uploadedServer = 1;
            $this->uploadToFtpServer();
        }
        
    }

    public function uploadToCurrentServer()
    {
        $file = $this->file;
        $location = 'assets/audios/';
        $path = $location.$this->date;

        try{
            $video = $this->date.'/'.fileUploader($file,$path,null);
            $this->fileName = $video;
        }catch (\Exception $exp) {
            $this->error = true;
        }
    }

    public function uploadToFtpServer()
    {
        $file = $this->file;
        $location = 'audios/';
        $path = $location.$this->date;

        try{
            $fileExtension = $file->getClientOriginalExtension();
            $this->configureDisk();

            $file = File::get($file);
            $disk = Storage::disk('custom-ftp');
     
            $this->ftpMakeDirectory($path,$disk);

            $video = uniqid().time().'.'.$fileExtension;
            $disk->put($path.'/'.$video, $file);
            $this->fileName = $path.'/'.$video;
            
        }catch(\Exception $e){
            $this->error = true;
        }
    }

    private function ftpMakeDirectory($path,$disk){
        if ($disk->exists($path)) {
            return true;
        }
        $disk->makeDirectory($path);
    }

    public function configureDisk()
    {
        $general = $this->general;
        //ftp

        Config::set('filesystems.disks.custom-ftp.driver','ftp');
        Config::set('filesystems.disks.custom-ftp.host',$general->ftp->host);
        Config::set('filesystems.disks.custom-ftp.username',$general->ftp->username);
        Config::set('filesystems.disks.custom-ftp.password',$general->ftp->password);
        Config::set('filesystems.disks.custom-ftp.port',21);
        Config::set('filesystems.disks.custom-ftp.root',$general->ftp->root);

    }

    public function removeFtpVideo()
    {
        $oldFile = $this->oldFile;
        $storage = Storage::disk('custom-ftp');
        if($storage->exists($oldFile)){
            $storage->delete($oldFile);
        }
        
    }

    public function removeOldFile(){
        if($this->oldServer == 0){
            $location = 'assets/audios/'.$this->oldFile;
            fileManager()->removeFile($location);
        }elseif($this->oldServer == 1){
            try{
                $this->configureDisk();
                $disk = Storage::disk('custom-ftp');
                $disk->delete($this->oldFile);
            }catch(\Exception $e){

            }
        }
    }


}