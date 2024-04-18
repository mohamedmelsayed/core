<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    public function playList(){
    	return $this->belongsTo(AudioPlaylist::class);
    }

    public function singleAudio(){
    	return $this->belongsTo(SingleAudio::class);
    }
}
