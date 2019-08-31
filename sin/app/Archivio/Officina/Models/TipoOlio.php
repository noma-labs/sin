<?php
namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *  Model per la tabella tipo_olio
 */
class TipoOlio extends Model
{
    protected $connection = 'db_officina';
    protected $table = 'tipo_olio';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['codice', 'note'];
}