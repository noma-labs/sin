<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Classe;
use Illuminate\Http\Request;

final class ClassiNoteController
{
    public function __invoke(Request $request, $id)
    {
        $anno = Classe::findOrFail($id);
        $anno->note = $request->get('note');
        $anno->save();

        return back()->withSuccess('Note aggiornate con successo.');
    }
}
