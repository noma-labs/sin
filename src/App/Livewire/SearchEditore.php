<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\Models\Editore;

final class SearchEditore extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $editori = Editore::query()
            ->where('Editore', 'LIKE', '%'.$term.'%')
            ->orderBy('editore')
            ->take(50)
            ->get();

        $options = [];
        foreach ($editori as $editore) {
            $options[] = new Option($editore->id, $editore->editore);
        }

        return $options;
    }

    public function selected(array|int $ids): array
    {
        $editori = Editore::query()
            ->whereIn('id', $ids)
            ->orderBy('editore', 'asc')
            ->get();

        $selected = [];
        foreach ($editori as $editore) {
            $selected[] = new Option($editore->id, $editore->editore);
        }

        return $selected;
    }
}
