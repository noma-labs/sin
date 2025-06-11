<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

final class PatentiController
{
    public function __invoke()
    {
        return view('officina.patenti');
    }
}
