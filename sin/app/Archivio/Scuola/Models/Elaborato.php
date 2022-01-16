<?php

namespace App\Scuola\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

//use Spatie\MediaLibrary\InteractsWithMedia;

class Elaborato extends Model implements HasMedia
{
    #use HasMediaTrait;
    use InteractsWithMedia;

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'elaborati';
    protected $primaryKey = "id";
    protected $guarded = [];

}