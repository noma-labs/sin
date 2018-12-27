<?php
namespace App\ArchivioDocumenti\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SortableTrait;

/**
 *
 */
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
    return $query->where('stato', 1)->orderBy("foglio");
  }

}
