<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as BaseTrimmer;

final class TrimStrings extends BaseTrimmer
{
    /**
     * The names of the attributes that should not be trimmed.
     */
    protected $except = [
        'password',
        'password_confirmation',
    ];
}
