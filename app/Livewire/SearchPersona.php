<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Date;

final class SearchPersona extends Autocomplete
{
    public function searchBy(string $term): array
    {
        $term = addcslashes($term, '%_\\');

        $query = $this->personeWithLatestStay();
        $query->where(fn (Builder $query): Builder => $query
            ->where('nominativo', 'LIKE', "$term%")
            ->orWhere('cognome', 'LIKE', "$term%")
            ->orWhere('nome', 'LIKE', "$term%"))
            ->limit(50);

        return $query->get()
            ->map(fn (Persona $persona): Option => $this->toOption($persona))
            ->all();
    }

    public function selected(array $ids): array
    {
        $query = $this->personeWithLatestStay();
        $query->whereIn('persone.id', $ids);

        return $query->get()
            ->map(fn (Persona $persona): Option => $this->toOption($persona))
            ->all();
    }

    /**
     * @return Builder<Persona>
     */
    private function personeWithLatestStay(): Builder
    {
        $query = Persona::query();
        $query->select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita', 'popolazione.data_entrata', 'popolazione.data_uscita')
            ->leftJoin('popolazione', function (JoinClause $join): void {
                $join->on('persone.id', '=', 'popolazione.persona_id')
                    ->whereRaw('popolazione.data_entrata = (SELECT MAX(p.data_entrata) FROM popolazione p WHERE p.persona_id = persone.id)');
            })
            ->orderBy('nominativo', 'asc');

        return $query;
    }

    private function toOption(Persona $persona): Option
    {
        $year = empty($persona->data_nascita) ? null : Date::parse($persona->data_nascita)->year;
        $label = ($year ? "($year) " : '')."$persona->nome  $persona->cognome ($persona->nominativo)";

        if (! empty($persona->data_entrata)) {
            $label .= ' '.$persona->data_entrata;
        }

        if (! empty($persona->data_uscita)) {
            $label .= ' - '.$persona->data_uscita;
        }

        return new Option($persona->id, $label);
    }
}
