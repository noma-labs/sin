<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Nomadelfia\Persona\Models\Persona;
use Carbon\Carbon;

final class SearchPersona extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $persone = Persona::query()
            ->selectRaw('persone.id, persone.nominativo, persone.nome, persone.cognome, persone.data_nascita, popolazione.data_entrata, MAX(popolazione.data_uscita) as data_uscita')
            ->leftJoin('popolazione', 'persone.id', '=', 'popolazione.persona_id')
            ->where('nominativo', 'LIKE', "$term%")
            ->orWhere('cognome', 'LIKE', "$term%")
            ->orWhere('nome', 'LIKE', "$term%")
            ->groupBy('persone.id')
            ->orderBy('nominativo', 'asc')
            ->limit(50)
            ->get();

        $options = [];
        foreach ($persone as $persona) {
            $year = Carbon::createFromFormat('Y-m-d', $persona->data_nascita)?->year;
            $details = ($year ? "($year) " : '')."$persona->nome  $persona->cognome ($persona->nominativo)";
            if (! empty($persona->data_entrata) && ! empty($persona->data_uscita)) {
                $details .= ' '.$persona->data_entrata.' - '.$persona->data_uscita;
            } elseif (! empty($persona->data_entrata)) {
                $details .= ' '.$persona->data_entrata;
            } elseif (! empty($persona->data_uscita)) {
                $details .= ' - '.$persona->data_uscita;
            }
            $options[] = new Option(
                $persona->id,
                $details
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
            $selected[] = new Option($persona->id, $persona->nominativo.' ('.$persona->data_nascita.')');
        }

        return $selected;
    }
}
