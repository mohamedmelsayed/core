<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use GlobalStatus, Searchable, ApiQuery;
    protected $fillable = ["name","name_en"];

    public function scopeActive() {
        return $this->where('status', 1);
    }

    public function subcategories() {
        return $this->hasMany(SubCategory::class);
    }
}
