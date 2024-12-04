<?php

declare(strict_types=1);

namespace App\Officina\Models;

use App\Traits\Enums;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codice
 * @property string $tipo
 * @property string $note
 */
final class TipoFiltro extends Model
{
    public $timestamps = false;

    protected $connection = 'db_officina';

    protected $table = 'tipo_filtro';

    protected $primaryKey = 'id';

    protected $fillable = ['codice', 'tipo', 'note'];

    /**
     * ritorna i possibili valori dell'enum tipo
     */
    public static function tipo(): array
    {
        return Enums::getPossibleEnumValues('tipo', 'db_meccanica.tipo_filtro');
    }
}
