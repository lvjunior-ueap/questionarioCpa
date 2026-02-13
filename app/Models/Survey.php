<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = [
        'title',
        'description',
        'active'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    protected static function booted(): void
    {
        static::saved(function (Survey $survey) {
            if (! $survey->active) {
                return;
            }

            static::where('id', '!=', $survey->id)
                ->where('active', true)
                ->update(['active' => false]);
        });
    }
}
