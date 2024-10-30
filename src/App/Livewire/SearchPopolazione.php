<?php

namespace App\Livewire;

use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;

class SearchPopolazione extends Autocomplete
{
    public function searchBy(string $term): array
    {

        $persone = PopolazioneNomadelfia::presenteByNomeCognomeNominativo($term)->orderBy('nominativo')->get();

        $options = [];
        foreach ($persone as $persona) {
            $year = Carbon::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $options[] = new Option($persona->id, "($year) $persona->nominativo ($persona->nome  $persona->cognome)");
        }

        return $options;
    }

    public function selected(array|int $ids): array
    {
        $persone =  Persona::query()
            ->whereIn('id', $ids)
            ->orderBy('nominativo')
            ->get();

        $selected = [];
        foreach ($persone as $persona) {
            $year = Carbon::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $selected[] = new Option($persona->id, "($year) $persona->nominativo ($persona->nome  $persona->cognome)");
        }

        return $selected;
    }
}
