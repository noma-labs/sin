<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Http\Request;

final class BooksCallNumberController
{
    public function show($idLibro)
    {
        $libro = Libro::with('classificazione')->findOrFail($idLibro);

        return view('biblioteca.books.call-number.show', compact('libro'));
    }

    public function update(Request $request, $idLibro)
    {
        $request->validate([
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
                return redirect()->route('books.call-number.swap', ['id' => $libro->id, 'idTarget' => $libroTarget->id]);
            }
            $libro->collocazione = $xCollocazioneNuova;
            $res = $libro->save();

            return redirect()->route('books.show', ['id' => $libro->id])->withSuccess("La collocazione $collocazione è stata sostituita in $libro->collocazione con successo.");

        }
        $libro->collocazione = null;
        $libro->save();

        return redirect()->route('books.show', ['id' => $libro->id])->withSuccess("La collocazione $collocazione è stata sostituita con SENZA COLLOCAZIONE con successo.");

    }

    public function swapShow($id, $idTarget)
    {
        $libro = Libro::findOrFail($id);
        $libroTarget = Libro::findOrFail($idTarget);

        return view('biblioteca.books.call-number.swap', compact('libro', 'libroTarget'));
    }

    public function swapUpdate($idLibro, $idTarget)
    {
        $libro = Libro::findOrFail($idLibro);
        $libroTarget = Libro::findOrFail($idTarget);

        $collocazione = $libro->collocazione;
        $libro->collocazione = $libroTarget->collocazione;
        $libroTarget->collocazione = $collocazione;
        $libro->save();
        $libroTarget->save();

        return redirect()->route('books.show', ['id' => $libro->id])
            ->withSuccess("Il libro $libro->titolo assegnato la collocazione $libro->collocazione, $libroTarget->titolo è stata sostituita in $libroTarget->collocazione con successo.");
    }
}
