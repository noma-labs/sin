<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\Persona\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

final class PersonaQueryBuilder extends Builder
{
    public function NumeroElencoPrefixByLetter(string $lettera)
    {
        return $this->selectRaw('persone.nome, persone.cognome, persone.numero_elenco, CAST(right(numero_elenco, length(numero_elenco)-1) as integer) as numero')
            ->whereRaw('numero_elenco is not null AND numero_elenco REGEXP :regex and left(numero_elenco,1) = :letter and persone.deleted_at is null', ['regex' => '^[a-zA-Z].*[0-9]$', 'letter' => $lettera])
            ->orderBy('numero', 'DESC');
    }

    public function natiInAnno(int $anno): Builder
    {
        return $this->whereRaw('YEAR(data_nascita)= ?', [$anno]);
    }
}
