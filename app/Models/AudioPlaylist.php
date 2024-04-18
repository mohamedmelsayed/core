<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioPlaylist extends Model
{
    public function audio()
    {
    	return $this->hasOne(Audio::class);
    }

    public function singleAudio()
    {
    	return $this->belongsTo(SingleAudio::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    public function planType()
    {
        return $this->belongsTo(PlanType::class,"version","level");
    }

    public function scopeHasAudio()
    {
    	return $this->where('status',1)->whereHas('video');
    }
}
