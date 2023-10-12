<?php

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $codice
 * @property string $note
 */
class TipoOlio extends Model
{
    protected $connection = 'db_officina';

    protected $table = 'tipo_olio';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = ['codice', 'note'];
}
