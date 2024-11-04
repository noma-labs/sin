<?php

namespace App\Livewire;

use Domain\Nomadelfia\Persona\Models\Persona;

class SearchAlunno extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $persone = Persona::query()
            ->selectRaw('persone.id, persone.nome, persone.cognome, persone.data_nascita, popolazione.data_entrata, MAX(popolazione.data_uscita) as data_uscita')
            ->leftJoin('popolazione', 'persone.id', '=', 'popolazione.persona_id')
            ->where('nominativo', 'LIKE', "$term%")->orWhere('cognome', 'LIKE', "$term%")->orWhere('nome', 'LIKE', "$term%")
            ->groupBy('persone.id')
            ->orderBy('nominativo', 'asc')
            ->get();

        $options = [];
        foreach ($persone as $persona) {
            $options[] = new Option(
                $persona->id,
                sprintf('%s %s (%s) [%s-%s]', $persona->nome, $persona->cognome, $persona->data_nascita, $persona->data_entrata, $persona->data_uscita)
            );
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
            $selected[] = new Option(
                $persona->id,
                sprintf('%s %s (%s)', $persona->nome, $persona->cognome, $persona->data_nascita)
            );
        }

        return $selected;
    }
}
