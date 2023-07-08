<?php

namespace Domain\Photo\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes, HasFactory;

    protected $casts = [
        'TakenAt' => 'datetime',
    ];
}
