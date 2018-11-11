<?php
namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;

class ViewClientiConPatente extends Model
{
    protected $table = 'v_cliente_patente';
    protected $connection = 'db_patente';
    protected $primaryKey = "persona_id";

}
