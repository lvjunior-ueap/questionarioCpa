<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveySession extends Model
{
    protected $fillable = [
        'token',
        'audience_id',
        'answers',
        'seen_dimensions',
        'completed_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'seen_dimensions' => 'array',
        'completed_at' => 'datetime',
    ];
}
