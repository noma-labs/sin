<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codice
 * @property string $note
 */
final class TipoOlio extends Model
{
    public $timestamps = false;

    protected $connection = 'db_officina';

    protected $table = 'tipo_olio';

    protected $primaryKey = 'id';

    protected $fillable = ['codice', 'note'];
}
