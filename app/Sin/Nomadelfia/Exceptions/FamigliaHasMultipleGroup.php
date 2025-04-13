<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use App\Nomadelfia\Famiglia\Models\Famiglia;

final class FamigliaHasMultipleGroup extends NomadelfiaException
{
    public static function named(Famiglia $famiglia): self
    {
        return new self("Il capo famiglia della famiglia `{$famiglia->nome_famiglia}` ha più di un gruppp familiare associato.");
    }
}
