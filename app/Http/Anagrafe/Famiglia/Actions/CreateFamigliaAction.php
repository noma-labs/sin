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
 * A genitore leaves the famiglia in which is a figlio. A genitore already capofamiglia or moglie in the nucleo of another famiglia,
 * and a figlio or aggregato already in the nucleo of another famiglia, stay there and are recorded in the new famiglia outside the nucleo, to keep the storico.
 */
final class CreateFamigliaAction
{
    /**
     * @param  list<Persona>  $figliNati
     * @param  list<Persona>  $figliAccolti
     * @param  list<Persona>  $aggregati
     */
    public function execute(
        TipoFamiglia $tipo,
        ?Carbon $dataCreazione,
        Persona $capofamiglia,
        ?Persona $moglie = null,
        array $figliNati = [],
        array $figliAccolti = [],
        ?string $nomeFamiglia = null,
        array $aggregati = [],
    ): Famiglia {
        return DB::connection('db_nomadelfia')->transaction(function () use ($tipo, $dataCreazione, $capofamiglia, $moglie, $figliNati, $figliAccolti, $nomeFamiglia, $aggregati): Famiglia {
            $famiglia = Famiglia::query()->create([
                'nome_famiglia' => $nomeFamiglia ?: $capofamiglia->nominativo,
                'tipo' => $tipo,
                'data_creazione' => $dataCreazione,
            ]);

            $isCapofamigliaNelloStorico = $this->isGenitoreInAltraFamiglia($capofamiglia);
            $isMoglieNelloStorico = $moglie instanceof Persona && $this->isGenitoreInAltraFamiglia($moglie);
            $genitoriNelNucleo = array_filter([
                $isCapofamigliaNelloStorico ? null : $capofamiglia,
                $isMoglieNelloStorico ? null : $moglie,
            ]);
            DB::connection('db_nomadelfia')->table('famiglie_persone')
                ->whereIn('persona_id', array_map(fn (Persona $genitore): int => $genitore->id, $genitoriNelNucleo))
                ->where('stato', '1')
                ->update(['stato' => '0']);

            $famiglia->assegnaCapoFamiglia($capofamiglia, null, $isCapofamigliaNelloStorico ? '0' : '1');
            if ($moglie instanceof Persona) {
                $famiglia->assegnaMoglie($moglie, $isMoglieNelloStorico ? '0' : '1');
            }

            foreach ($figliNati as $figlio) {
                $famiglia->assegnaFiglioNato($figlio, null, $this->statoFiglio($figlio, $famiglia));
            }
            foreach ($figliAccolti as $figlio) {
                $famiglia->assegnaFiglioAccolto($figlio, null, $this->statoFiglio($figlio, $famiglia));
            }
            foreach ($aggregati as $figlio) {
                $famiglia->assegnaAggregato($figlio, null, $this->statoFiglio($figlio, $famiglia));
            }

            return $famiglia;
        });
    }

    /**
     * Whether the genitore is already capofamiglia or moglie in the nucleo of another famiglia: he stays there
     * and enters the new famiglia outside the nucleo. Otherwise he leaves the famiglia in which is a figlio.
     */
    private function isGenitoreInAltraFamiglia(Persona $genitore): bool
    {
        return DB::connection('db_nomadelfia')->table('famiglie_persone')
            ->where('persona_id', $genitore->id)
            ->where('stato', '1')
            ->whereIn('posizione_famiglia', ['CAPO FAMIGLIA', 'MOGLIE'])
            ->exists();
    }

    /**
     * The figlio is in the nucleo of the new famiglia, unless he is already in the nucleo of another famiglia.
     */
    private function statoFiglio(Persona $figlio, Famiglia $famiglia): string
    {
        $isInAltraFamiglia = DB::connection('db_nomadelfia')->table('famiglie_persone')
            ->where('persona_id', $figlio->id)
            ->where('famiglia_id', '!=', $famiglia->id)
            ->where('stato', '1')
            ->exists();

        return $isInAltraFamiglia ? '0' : '1';
    }
}
