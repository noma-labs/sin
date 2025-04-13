<?php

declare(strict_types=1);

namespace App\Nomadelfia\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class NomadelfiaException extends Exception
{
    final public function render(Request $request): Response
    {
        $exception = $this;

        return response()->view('errors.sin-error', compact('exception'), 500);
    }
}
