<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Biblioteca\Models\ViewCollocazione;

final class SearchCollocazioneLettere extends Autocomplete
{
    /**
     * Restrict the suggested lettere to the ones starting with this prefix (e.g. "SP").
     * When set, the "SENZA COLLOCAZIONE" option is not offered.
     */
    public string $prefix = '';

    public function searchBy(string $term): array
    {
        $collocazioni = ViewCollocazione::lettere()
            ->where('lettere', 'LIKE', $term.'%')
            ->when($this->prefix !== '', fn ($query) => $query->where('lettere', 'LIKE', $this->prefix.'%'))
            ->get();

        $options = $this->prefix === '' ? [new Option('SENZA_COLLOCAZIONE', 'SENZA COLLOCAZIONE')] : [];
        foreach ($collocazioni as $colloc) {
            $options[] = new Option($colloc->lettere, $colloc->lettere);
        }

        return $options;
    }

    public function selected(array|int|string $ids): array
    {
        $collocazioni = ViewCollocazione::query()
            ->whereIn('COLLOCAZIONE', $ids)
            ->orderBy('COLLOCAZIONE')
            ->get();

        $selected = [];
        foreach ($collocazioni as $colloc) {
            $options[] = new Option($colloc->lettere, $colloc->lettere);
        }

        return $selected;
    }
}
