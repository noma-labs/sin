<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\Models\Anno;
use Illuminate\Http\Request;

final class AnnoScolasticoNoteController
{
    public function __invoke(Request $request, $id)
    {
        $anno = Anno::find($id);
        $anno->descrizione = $request->get('note');
        $anno->save();

        return back()->withSuccess('Note aggiornate con successo.');
    }
}
