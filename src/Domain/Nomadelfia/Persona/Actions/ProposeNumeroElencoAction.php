<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\Persona\Actions;

use Domain\Nomadelfia\Persona\Models\Persona;
use Exception;
use Illuminate\Support\Str;

final class ProposeNumeroElencoAction
{
    public function execute(Persona $persona): string
    {
        if ($persona->numero_elenco) {
            throw new Exception('La persona '.$persona->nominativo.' ha già un numero di elenco '.$persona->numero_elenco);
        }
        $firstLetter = Str::substr($persona->cognome, 0, 1);
        $res = Persona::selectRaw('left(numero_elenco,1) as  lettera, CAST(right(numero_elenco, length(numero_elenco)-1) as integer)  as numero')
            ->whereRaw('numero_elenco is not null AND numero_elenco REGEXP ? and left(numero_elenco,1) = ?', ['^[a-zA-Z].*[0-9]$', $firstLetter])
            ->orderBy('numero', 'DESC')
            ->first();
        if ($res) {
            $new = (int) $res->numero + 1;

            return $res->lettera.$new;
        }

        return $firstLetter.'1';
    }
}
