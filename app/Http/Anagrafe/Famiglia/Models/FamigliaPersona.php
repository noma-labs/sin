<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * A persona in a famiglia (table famiglie_persone).
 *
 * @property string $posizione_famiglia
 * @property '0'|'1' $stato '1' in the nucleo of the famiglia, '0' outside the nucleo (history)
 * @property string|null $note
 * @property string|null $data_entrata the entrata in the famiglia when it is not derived (from the birth, the data_creazione
 *                                     of the famiglia or the entrata in the comunità), e.g. a figlio accolto moved to another famiglia
 */
final class FamigliaPersona extends Pivot
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'famiglie_persone';
}
