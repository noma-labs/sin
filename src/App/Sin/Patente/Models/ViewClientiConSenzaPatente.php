<?php

declare(strict_types=1);

namespace App\Patente\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $cliente_con_patente.
 * @property string $cognome
 * @property string $nome
 * @property string $data_nascita
 */
final class ViewClientiConSenzaPatente extends Model
{
    protected $table = 'v_clienti_patente';

    protected $connection = 'db_patente';

    protected $primaryKey = 'persona_id';

    /**
     * Ritorna  clienti che hanno la patente
     *
     * @return ViewClientiConSenzaPatente[]
     */
    public function scopeConPatente($query)
    {
        return $query->where('cliente_con_patente', 'CP');
    }

    /**
     * Ritorna  clienti che hanno la patente
     *
     * @return ViewClientiConSenzaPatente[]
     */
    public function scopeSenzaPatente($query)
    {
        return $query->where('cliente_con_patente', '!=', 'CP')->orWhereNull('cliente_con_patente');
    }

    public function patenti()
    {
        return $this->hasMany(Patente::class, 'persona_id', 'persona_id');
    }
}
