<?php

namespace App\Nomadelfia\Exceptions;

use InvalidArgumentException;

class SpostaNellaFamigliaError extends InvalidArgumentException
{
    public static function create(string $nominativo, string $famiglia, string $msg = ''): SpostaNellaFamigliaError
    {
        return new self("Impossibile spostare {$nominativo} nella famiglia  {$famiglia}. {$msg}");
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {

        $exception = $this;

        return response()->view('errors.sinError', compact('exception'), 500);
    }
}
