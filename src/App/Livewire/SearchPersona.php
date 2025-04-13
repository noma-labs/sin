<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Nomadelfia\Persona\Models\Persona;

final class SearchPersona extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $persone = Persona::query()
            ->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita')
            ->where('nominativo', 'LIKE', "$term%")
            ->orderBy('nominativo', 'asc')
            ->get();

        $options = [];
        foreach ($persone as $persona) {
            $options[] = new Option($persona->id, $persona->nominativo.' ('.$persona->data_nascita.')');
        }

        return $options;
    }

    public function selected(array $ids): array
    {
        $q = Persona::query()
            ->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita')
            ->whereIn('id', $ids)
            ->orderBy('nominativo', 'asc');

        $persone = $q->get();

        $selected = [];
        foreach ($persone as $persona) {
            $selected[] = new Option($persona->id, $persona->nominativo.' ('.$persona->data_nascita.')');
        }

        return $selected;
    }
}
