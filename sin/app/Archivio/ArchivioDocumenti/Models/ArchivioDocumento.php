<?php
namespace App\ArchivioDocumenti\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class ArchivioDocumento extends Model
{
  protected $connection = 'archivio_documenti';
  protected $table = 'prova_tabella';
  protected $primaryKey = 'id';

  public $timestamps = false;
  protected $guarded = ['id'];


  public function scopeTobePrinted($query)
  {
    return $query->where('tobe_printed', 1)->orderBy("collocazione");
  }

}
