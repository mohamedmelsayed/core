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

    // Accessor for dynamic title
    public function getDynamicTitleAttribute()
    {
        $language = request()->header('Language', 'en'); // Default to 'en'
        $language = in_array($language, ['ar', 'en']) ? $language : 'en'; // Ensure valid value
        return $language === 'ar' ? $this->title : $this->title_en;
    }

    // Accessor for dynamic description
    public function getDynamicDescriptionAttribute()
    {
        $language = request()->header('Language', 'en'); // Default to 'en'
        $language = in_array($language, ['ar', 'en']) ? $language : 'en'; // Ensure valid value
        return $language === 'ar' ? $this->description : $this->description_en;
    }
}
