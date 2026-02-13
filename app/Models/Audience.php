<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'intro_text',
    ];

    public function dimensions()
    {
        return $this->hasMany(Dimension::class);
    }
}
