<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\Models\Autore;

final class SearchAutore extends Autocomplete
{
    public function searchBy(string $term): array
    {

        $autori = Autore::query()->where('autore', 'LIKE', '%'.$term.'%')
            ->orderBy('autore')
            ->take(50)
            ->get();
        $options = [];
        foreach ($autori as $autore) {
            $options[] = new Option($autore->id, $autore->autore);
        }

        return $options;
    }

    public function selected(array|int $ids): array
    {
        $autori = Autore::query()->whereIn('id', $ids)
            ->orderBy('autore', 'asc')
            ->get();

        $selected = [];
        foreach ($autori as $autore) {
            $selected[] = new Option($autore->id, $autore->autore);
        }

        return $selected;
    }
}
