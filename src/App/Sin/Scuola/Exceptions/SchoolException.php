<?php

declare(strict_types=1);

namespace App\Scuola\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SchoolException extends Exception
{
    final public function render(Request $request): Response
    {
        $exception = $this;

        return response()->view('errors.sin-error', compact('exception'), 500);
    }
}
