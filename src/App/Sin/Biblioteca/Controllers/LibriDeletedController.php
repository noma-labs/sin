<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Http\Request;

final class LibriDeletedController
{
    public function index()
    {
        $libriEliminati = Libro::onlyTrashed()->paginate(50);

        return view('biblioteca.libri.deleted.index', compact('libriEliminati'));
    }

    public function create($idLibro)
    {
        $libro = Libro::findOrFail($idLibro);

        return view('biblioteca.libri.deleted.create', ['libro' => $libro]);
    }

    public function restore($idLibro)
    {
        $libro = Libro::withTrashed()->findOrFail($idLibro);
        $libro->restore();

        return redirect()->route('libro.dettaglio', ['idLibro' => $libro])->withSuccess('Il libro è stato ripristinato con successo');
    }

    public function deleteConfirm(Request $request, $idLibro)
    {
        $request->validate([
            'xCancellazioneNote' => 'required', //per update solito nome
        ], [
            'xCancellazioneNote.required' => 'La motivazione della cancellazione del libro è obbligatoria.',
        ]);
        $libro = Libro::findOrFail($idLibro);
        if ($libro->inPrestito()) {
            return redirect()->route('books.index')
                ->withError('Impossibilie eliminare il libro. Il libro è in prestito.');
        }

        $libro->deleted_note = $request->xCancellazioneNote;
        $libro->delete();

        return redirect()->route('books.index')->withSuccess('Il libro è stato eliminato con successo.');
    }
}
