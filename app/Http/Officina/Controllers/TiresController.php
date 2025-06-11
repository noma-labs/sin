<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\TipoGomme;
use Illuminate\Http\Request;

final class TiresController
{
    public function store(Request $request)
    {
        $request->validate([
            'codice' => 'required',
        ]);
        $gomma = TipoGomme::create([
            'codice' => $request->input('codice'),
        ]);

        return redirect()->back()->withSuccess("Gomma $gomma->codice salvata correttamente");
    }
}
