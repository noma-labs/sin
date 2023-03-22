<?php
namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SortableTrait;

class Video extends Model {
  use SortableTrait;

  protected $connection = 'db_biblioteca';
  protected $table = 'video';
  protected $primaryKey = "id";
  protected $dates = ['data_registrazione'];

}
