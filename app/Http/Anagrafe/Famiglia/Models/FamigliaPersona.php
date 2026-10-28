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
 */
final class FamigliaPersona extends Pivot
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'famiglie_persone';
}
