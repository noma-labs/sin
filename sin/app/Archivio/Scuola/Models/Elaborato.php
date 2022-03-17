<?php

namespace App\Scuola\Models;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Elaborato extends BaseMedia
{
    #use HasMediaTrait;
    use InteractsWithMedia;

    public $timestamps = true;

    protected $connection = 'db_scuola';
    protected $table = 'media';
    protected $primaryKey = "id";
    protected $guarded = [];

}