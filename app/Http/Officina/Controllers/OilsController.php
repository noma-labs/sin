<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\TipoOlio;
use Illuminate\Http\Request;

final class OilsController
{
    public function store(Request $request)
    {
        $request->validate([
            'codice' => 'required',
        ]);
        $olio = TipoOlio::create([
            'codice' => mb_strtoupper((string) $request->input('codice')),
            'note' => $request->input('note', ''),
        ]);

        return redirect()->back()->withSuccess("Olio $olio->codice salvato correttamente");
    }
}
