<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Anagrafe\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use App\View\Components\PersonaNome;

final class SearchPopolazione extends Autocomplete
{
    public function searchBy(string $term): array
    {

        // persone.updated_at again after popolazione.*, which has its own updated_at: the URL of the avatar changes with the persona
        $persone = PopolazioneNomadelfia::presenteByNomeCognomeNominativo($term)->addSelect('persone.updated_at')->orderBy('nominativo')->get();

        $options = [];
        foreach ($persone as $persona) {
            $year = \Illuminate\Support\Facades\Date::createFromFormat('Y-m-d', $persona->data_nascita)->year;
            $options[] = Option::perPersona($persona, "($year) ".PersonaNome::testo($persona));
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
            $selected[] = Option::perPersona($persona, "($year) ".PersonaNome::testo($persona));
        }

        return $selected;
    }
}
