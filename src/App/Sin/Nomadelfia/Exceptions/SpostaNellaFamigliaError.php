<?php

namespace App\Nomadelfia\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SpostaNellaFamigliaError extends NomadelfiaException
{
    public static function create(string $nominativo, string $famiglia, string $msg = ''): SpostaNellaFamigliaError
    {
        return new self("Impossibile spostare {$nominativo} nella famiglia  {$famiglia}. {$msg}");
    }
}
