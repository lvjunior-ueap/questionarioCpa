<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = [
        'questionnaire_id',
        'perfil'
    ];
    
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}