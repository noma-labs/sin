<?php

namespace App\Rtn\Persone;

use App\Traits\SortableTrait;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Alias extends Model
{
    use SortableTrait;

    protected $connection = 'db_rtn';

    protected $table = 'persone_alias';

    public static function startWith(string $term): Builder
    {
        return self::select('persone.id', 'persone.nominativo', 'persone.nome', 'persone.cognome', 'persone.data_nascita', 'persone_alias.alias')
            ->join('db_nomadelfia.persone', 'persone.id', '=', 'persone_alias.persona_id')
            ->where('alias', 'like', $term.'%');
    }

    public static function ofPersona(Persona $persona): Builder
    {
        return self::where('persona_id', $persona->id);
    }
}
