<?php

namespace App\Nomadelfia\Exceptions;

use Domain\Nomadelfia\Famiglia\Models\Famiglia;

class FamigliaHasMultipleGroup extends NomadelfiaException
{
    public static function named(Famiglia $famiglia): FamigliaHasMultipleGroup
    {
        return new self("Il capo famiglia della famiglia `{$famiglia->nome_famiglia}` ha più di un gruppp familiare associato.");
    }
}
