<?php

namespace App\Anagrafe\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
  protected $connection = 'db_anagrafe';

  protected $table = 'provincie';
}
