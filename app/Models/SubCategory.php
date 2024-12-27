<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model {
    use GlobalStatus, Searchable, ApiQuery;
    
    protected $guarded = ['id'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function getDynamicNameAttribute() {
        $language = request()->header('Language', 'en'); // Default to 'en'
        $language = in_array($language, ['ar', 'en']) ? $language : 'en'; // Ensure valid value
        return $language === 'ar' ? ($this->name ?? '') : ($this->name_en ?? '');
    }
}
