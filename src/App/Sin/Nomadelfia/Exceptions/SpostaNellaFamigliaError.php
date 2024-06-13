<?php

namespace App\Nomadelfia\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @param  Request  $request
     * @return Response
     */
    public function render($request)
    {

        $exception = $this;

        return response()->view('errors.sinError', ['exception' => $exception], 500);
    }
}
