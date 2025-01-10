<?php

declare(strict_types=1);

namespace App\Livewire;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;

final class SearchFamiglia extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $families = Famiglia::query()
            ->select('id', 'nome_famiglia')
            ->orderBy('nome_famiglia', 'asc')
            ->get();

        $options = [];
        foreach ($families as $family) {
            $options[] = new Option($family->id, $family->nome_famiglia);
        }

        return $options;
    }

    public function selected(array $ids): array
    {
        $families = Famiglia::query()
            ->select('id', 'nome_famiglia')
            ->orderBy('nome_famiglia', 'asc')
            ->whereIn('id', $ids)
            ->get();

        $selected = [];
        foreach ($families as $family) {
            $selected[] = new Option($family->id, $family->nome_famiglia);
        }

        return $selected;
    }
}
