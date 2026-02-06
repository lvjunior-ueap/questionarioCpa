<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = [
        'survey_id',
        'perfil'
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }
}