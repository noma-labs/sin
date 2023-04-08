<?php

namespace App\ArchivioDocumenti\Models;

use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;

class ArchivioDocumento extends Model
{
  use SortableTrait;

  protected $connection = 'archivio_documenti';

  protected $table = 'libri';

  protected $primaryKey = 'id';

  public $timestamps = false;

  protected $guarded = ['id'];

  public function scopeTobePrinted($query)
  {
    return $query->where('stato', 1)->orderBy('foglio');
  }
}
