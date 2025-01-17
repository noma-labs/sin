<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\Models\ViewCollocazione;

final class SearchCollocazioneLettere extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $collocazioni = ViewCollocazione::lettere()->where('lettere', 'LIKE', $term.'%')->get();

        $options = [new Option('SENZA_COLLOCAZIONE', 'SENZA COLLOCAZIONE')];
        foreach ($collocazioni as $colloc) {
            $options[] = new Option($colloc->lettere, $colloc->lettere);
        }

        return $options;
    }

    public function selected(array|int|string $ids): array
    {
        $collocazioni = ViewCollocazione::query()
            ->whereIn('COLLOCAZIONE', $ids)
            ->orderBy('COLLOCAZIONE')
            ->get();

        $selected = [];
        foreach ($collocazioni as $colloc) {
            $options[] = new Option($colloc->lettere, $colloc->lettere);
        }

        return $selected;
    }
}
