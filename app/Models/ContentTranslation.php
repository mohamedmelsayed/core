<?php

namespace App\Models;

use App\Traits\ApiQuery;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class ContentTranslation extends Model {
    use Searchable, GlobalStatus, Searchable, ApiQuery;

    protected $fillable = [
        'translated_tags','translated_description','translated_title','translated_keywords','type '
    ];

}
