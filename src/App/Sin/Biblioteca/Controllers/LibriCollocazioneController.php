<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Http\Request;

final class LibriCollocazioneController
{
    public function show($idLibro)
    {
        $libro = Libro::with('classificazione')->findOrFail($idLibro);

        return view('biblioteca.libri.collocazione', compact('libro'));
    }

    public function updateCollocazione(Request $request, $idLibro)
    {
        $validatedData = $request->validate([
            'xCollocazione' => 'required', //per update solito nome
        ], [
            'xCollocazione.required' => 'La collocazione nuova non è stata selezionata.',
        ]);

        $libro = Libro::findOrFail($idLibro);
        $collocazione = $libro->collocazione;
        $xCollocazioneNuova = $request->xCollocazione;
        if ($xCollocazioneNuova !== 'null') {
            $libroTarget = Libro::where('collocazione', $xCollocazioneNuova)->first();
            if ($libroTarget) {
                return view('biblioteca.libri.collocazione_confirm', compact('libro', 'libroTarget'))->withWarning('Stai cambiando la collocazione');
            }
            $libro->collocazione = $xCollocazioneNuova;
            $res = $libro->save();

            return redirect()->route('libro.dettaglio', ['idLibro' => $libro->id])->withSuccess("La collocazione $collocazione è stata sostituita in $libro->collocazione con successo.");

        }
        $libro->collocazione = null;
        $res = $libro->save();

        return redirect()->route('libro.dettaglio', ['idLibro' => $libro->id])->withSuccess("La collocazione $collocazione è stata sostituita con SENZA COLLOCAZIONE con successo.");

    }

    public function confirmCollocazione(Request $request, $idLibro)
    {
        $request->validate([
            'idTarget' => 'required', //per update solito nome
        ], [
            'idTarget.required' => 'IL libro a cui prelevare la collocazione è obbligatorio.',
        ]);

        $libro = Libro::findOrFail($idLibro);
        $libroTarget = Libro::findOrFail($request->idTarget);

        $collocazione = $libro->collocazione;
        $libro->collocazione = $libroTarget->collocazione;
        $libroTarget->collocazione = $collocazione;
        $libro->save();
        $libroTarget->save();

        return redirect()->route('libro.dettaglio', ['idLibro' => $libro->id])
            ->withSuccess("Il libro $libro->titolo assegnato la collocazione $libro->collocazione, $libroTarget->titolo è stata sostituita in $libroTarget->collocazione con successo.");
    }
}
