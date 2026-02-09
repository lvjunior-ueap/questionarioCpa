<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = [
        'survey_id',
        'audience_id'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}
