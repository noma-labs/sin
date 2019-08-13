<?php
namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Enums;

/**
 *
 */
class TipoFiltro extends Model
{
    protected $connection = 'db_officina';
    protected $table = 'tipo_filtro';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['codice', 'tipo', 'note'];

    /**
     * ritorna i possibili valori dell'enum tipo
     */
    public static function tipo(){
        return Enums::getPossibleEnumValues('tipo', 'db_meccanica.tipo_filtro');
    }
}