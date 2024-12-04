<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Scuola\Models\Elaborato;

final class SearchElaborato extends Autocomplete
{
    public function searchBy(string $term): array
    {

        $elaborati = Elaborato::query()
            ->select('id', 'collocazione', 'anno_scolastico', 'titolo')
            ->where('collocazione', 'LIKE', "$term%")
            ->orderBy('collocazione', 'asc')
            ->get();

        $options = [];

        foreach ($elaborati as $elaborato) {
            $options[] = new Option($elaborato->id, $elaborato->collocazione.' '.$elaborato->titolo.' ( '.$elaborato->anno_scolastico.' )');
        }

        return $options;
    }

    public function selected(array|int $ids): array
    {
        $elaborati = Elaborato::query()
            ->select('id', 'collocazione', 'anno_scolastico', 'titolo')
            ->whereIn('id', $ids)
            ->orderBy('collocazione', 'asc')
            ->get();

        $selected = [];
        foreach ($elaborati as $elaborato) {
            $selected[] = new Option($elaborato->id, $elaborato->collocazione.' '.$elaborato->titolo.' ');
        }

        return $selected;
    }
}
