<?php

declare(strict_types=1);

namespace App\Anagrafe\Famiglia\Actions;

use App\Anagrafe\Famiglia\Models\Famiglia;
use App\Anagrafe\Famiglia\Models\TipoFamiglia;
use App\Anagrafe\Persona\Models\Persona;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Creates a famiglia in the anagrafe. The componenti can be part of the popolazione or not: nobody enters or leaves the popolazione.
 */
final class CreateFamigliaAction
{
    /**
     * @param  list<Persona>  $figliNati
     * @param  list<Persona>  $figliAccolti
     */
    public function execute(
        TipoFamiglia $tipo,
        ?Carbon $dataCreazione,
        Persona $capofamiglia,
        ?Persona $moglie = null,
        array $figliNati = [],
        array $figliAccolti = [],
        ?string $nomeFamiglia = null,
    ): Famiglia {
        return DB::connection('db_nomadelfia')->transaction(function () use ($tipo, $dataCreazione, $capofamiglia, $moglie, $figliNati, $figliAccolti, $nomeFamiglia): Famiglia {
            $famiglia = Famiglia::query()->create([
                'nome_famiglia' => $nomeFamiglia ?: $capofamiglia->nominativo,
                'tipo' => $tipo,
                'data_creazione' => $dataCreazione,
            ]);

            $genitori = array_filter([$capofamiglia, $moglie]);
            DB::connection('db_nomadelfia')->table('famiglie_persone')
                ->whereIn('persona_id', array_map(fn (Persona $genitore): int => $genitore->id, $genitori))
                ->where('stato', '1')
                ->update(['stato' => '0']);

            $famiglia->assegnaCapoFamiglia($capofamiglia);
            if ($moglie instanceof Persona) {
                $famiglia->assegnaMoglie($moglie);
            }

            foreach ($figliNati as $figlio) {
                $famiglia->assegnaFiglioNato($figlio);
            }
            foreach ($figliAccolti as $figlio) {
                $famiglia->assegnaFiglioAccolto($figlio);
            }

            return $famiglia;
        });
    }
}
