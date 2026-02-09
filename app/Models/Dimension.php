<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    protected $fillable = [
        'survey_id',
        'audience_id',
        'title',
        'description',
        'order'
    ];
}
