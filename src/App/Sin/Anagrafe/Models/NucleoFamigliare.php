<?php

namespace App\Anagrafe\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $nominativo
 * @property string $data_nascita
 * @property double $eta
 */
class NucleoFamigliare extends Model
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'nuclei_famigliari';

    protected $primaryKey = 'id';
}
