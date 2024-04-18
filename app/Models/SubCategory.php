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
}
