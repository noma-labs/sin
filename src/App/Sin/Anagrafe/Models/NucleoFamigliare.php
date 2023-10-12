<?php

namespace App\Anagrafe\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nominativo
 * @property string $data_nascita
 * @property float $eta
 */
class NucleoFamigliare extends Model
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'nuclei_famigliari';

    protected $primaryKey = 'id';
}
