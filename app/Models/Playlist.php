<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
         'title_en',
        'description_en',
        'type',
        'cover_image',
        'sub_category_id',
    ];

      // Define the many-to-many relationship with items
      public function items()
      {
          return $this->belongsToMany(Item::class, 'playlist_items', 'playlist_id', 'item_id');
      }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category()
    {
        return $this->subCategory->category();
    }
}
