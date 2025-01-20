<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\Prestito as Prestito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class BooksBorrowController
{
    public function create($id)
    {
        $libro = Libro::findOrFail($id);

        return view('biblioteca.libri.book', ['libro' => $libro]);
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'xDatainizio' => 'date',
            'persona_id' => 'required',
            // 'xIdBibliotecario'=> 'exists:db_ayth.cliente,id'
        ], [
            'xDatainizio.date' => 'La data di inizio prestito deve essere una data valida YYYY-MM-GG',
            'persona_id.exists' => 'Il cliente selezionanto non esiste',
            'persona_id.required' => 'Nessun cliente selezionato',
        ]);

        $libro = Libro::findOrFail($id);
        // Receive the data for the book with POST operation, and  redirect to the libro dettaglio
        $datainizio = $request->xDatainizio;
        $datafine = $request->xDataFine;
        $note = $request->input('xNote', null);
        $idUtente = $request->persona_id;

        // biblitoecario is the id of the Person associated with the logged user
        $idBibliotecario = Auth::user()->persona->id;

        if ($libro->inPrestito()) {
            return redirect()->back()->withError('Impossibile prenotare il libro, il  Libro è già in prestito');
        }
        $prestito = Prestito::create(['bibliotecario_id' => $idBibliotecario, 'libro_id' => $id, 'cliente_id' => $idUtente, 'data_inizio_prestito' => $datainizio, 'data_fine_prestito' => $datafine, 'in_prestito' => 1, 'note' => $note]);
        if ($prestito) {
            return redirect()->route('books.loans')->withSuccess('Prestitio andato a buon fine Libro: '.$prestito->libro->titolo.', Cliente:'.$prestito->cliente->nominativo.', Bibliotecario:'.$prestito->bibliotecario->nominativo);
        }
        redirect()->route('books.loans')->withWarning('Errore nel prestito');

    }
}
