<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\Media;

class MyMedia extends Media
{
   protected $connection = 'db_biblioteca';
   protected $table = 'media';
}
