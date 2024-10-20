<?php

namespace App\Nomadelfia\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class NomadelfiaException extends Exception
{
    public function render(Request $request): Response
    {
        $exception = $this;

        return response()->view('errors.sinError', compact('exception'), 500);
    }
}
