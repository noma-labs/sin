<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nominativo
 */
class ViewLavoratoriBiblioteca extends Model
{
    protected $connection = 'db_biblioteca';

    protected $table = 'v_lavoratori_biblioteca';

    protected $primaryKey = 'persona_id';
}
