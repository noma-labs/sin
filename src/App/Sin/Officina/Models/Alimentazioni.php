<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

class Alimentazioni extends Model
{
  protected $connection = 'db_officina';

  protected $table = 'alimentazione';

  protected $primaryKey = 'id';
}
