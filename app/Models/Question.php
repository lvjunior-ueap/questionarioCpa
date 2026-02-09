<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'survey_id',
        'dimension_id',
        'text',
        'type',
    ];

    public function dimension()
    {
        return $this->belongsTo(Dimension::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }
}
