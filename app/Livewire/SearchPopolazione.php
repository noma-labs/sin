<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

final class SearchPopolazione extends Autocomplete
{
    public function searchBy(string $term): array
    {

        $persone = PopolazioneNomadelfia::presenteByNomeCognomeNominativo($term)->orderBy('nominativo')->get();

        $options = [];
        foreach ($persone as $persona) {
            $year = \Illuminate\Support\Facades\Date::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $options[] = new Option($persona->id, "($year) $persona->nominativo ($persona->nome  $persona->cognome)");
        }

        return $options;
    }

    public function selected(array|int $ids): array
    {
        $persone = Persona::query()
            ->whereIn('id', $ids)
            ->orderBy('nominativo')
            ->get();

        $selected = [];
        foreach ($persone as $persona) {
            $year = \Illuminate\Support\Facades\Date::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $selected[] = new Option($persona->id, "($year) $persona->nominativo ($persona->nome  $persona->cognome)");
        }

        return $selected;
    }
}
