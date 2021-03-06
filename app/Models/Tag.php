<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }
}
