<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleAduio extends Model
{
    
    public function category()
    {
    	return $this->belongsTo(Category::class, 'category_id');
    }

    public function sub_category()
    {
    	return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

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




    public function scopeSingleItems()
    {
        return $this->where('item_type', 1);
    }

    public function scopeEpisodeItems()
    {
        return $this->where('item_type', 2);
    }

    public function scopeTrailerItems()
    {
        return $this->where('item_type', 3);
    }



    // Scopes free 

    public function scopeFree($q){
        return $this->where('status',1)->where('item_type','!=',3)->where(function($free){
                $free->orWhere('version',0)->orWhereHas('episodes',function($query){
                $query->where('version',0);
            });
        });
        // return $this->where('status',1)->where('item_type','!=',3)->where('version',0)->whereHas('episodes',function($free){
        //     $free->where('version',0);
        // });
    }
    public function scopeActive($q){
        return $this->where(function($query){
            $query->orWhere('status',1)->orWhereHas('episodes',function($episodes){
                $episodes->where('status',1);
            });
        });
    }

    public function scopeSearch($s, $search){
        return $this->where(function($query) use ($search){
            $query->orWhere('title','LIKE',"%$search%")
            ->orWhereHas('category',function($category) use ($search){
                $category->where('status',1)->where('name','LIKE',"%$search%");
            })
            ->orWhereHas('sub_category',function($sub_category) use ($search){
                $sub_category->where('status',1)->where('name','LIKE',"%$search%");
            })
            ->orWhereHas('episodes',function($episodes) use ($search){
                $episodes->where('status',1)->where('title','LIKE',"%$search%");
            })
            ->orWhere('preview_text','LIKE',"%$search%")
            ->orWhere('description','LIKE',"%$search%")
            ->orWhere('team','LIKE',"%$search%")
            ->orWhere('tags','LIKE',"%$search%");
        });
    }

}
