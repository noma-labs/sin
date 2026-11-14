<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolasticoData;
use App\Scuola\DataTransferObjects\CicloScolastico;
use App\Scuola\DataTransferObjects\Classe;
use App\Scuola\Models\Anno;
use Illuminate\Contracts\View\View;

/**
 * The page of an anno scolastico with the alunni divided by ciclo and classe, to print with the browser.
 */
final class AnnoScolasticoStampaController
{
    public function __invoke(int $id): View
    {
        $anno = AnnoScolasticoData::FromDatabase(Anno::query()->findOrFail($id));

        $cicli = collect([$anno->prescuola, $anno->elementari, $anno->medie, $anno->superiori, $anno->universita])
            ->filter(fn (CicloScolastico $ciclo): bool => $ciclo->alunniCount > 0)
            ->each(function (CicloScolastico $ciclo): void {
                foreach ($ciclo->classi ?? [] as $classe) {
                    $this->sortAlunniByNome($classe);
                }
            })
            ->values();

        return view('scuola.anno.stampa', [
            'anno' => $anno,
            'cicli' => $cicli,
        ]);
    }

    private function sortAlunniByNome(Classe $classe): void
    {
        $classe->alunni = collect($classe->alunni)
            ->sortBy([['nome', 'asc'], ['cognome', 'asc']])
            ->values()
            ->all();
    }
}
