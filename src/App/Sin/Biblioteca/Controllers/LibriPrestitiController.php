<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use App\Biblioteca\Models\Prestito as Prestito;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

final class LibriPrestitiController
{
    public function conclude(Request $request, $idPrestito)
    {
        $prestito = Prestito::findOrFail($idPrestito);
        $_concludi = $request->_concludi; // "concludi" value is sent when the button Concluid prestito is clicked

        // $bibliotecario = Auth::user()->id; //$request->xIdBibliotecario;
        $bibliotecario = Auth::user()->persona->id;
        if ($_concludi) {
            $data = Carbon::now()->toDateString();
            $res = $prestito->update(['in_prestito' => 0, 'data_fine_prestito' => $data, 'bibliotecario_id' => $bibliotecario]);
            if ($res) {
                return ($url = Session::get('clientePrestitiUrl'))
                    ? redirect()->to($url)->withSuccess("Prestito terminato correttamente in data $data")
                    : redirect()->route('libri.prestiti')->withSuccess("Prestito terminato correttamente in data $data");
            }

            return redirect()->route('libri.prestiti')->withError('Errore nella richiesta. Nessuna modifica effettuata');

        }

    }

    public function view()
    {
        $prestiti = Prestito::inPrestito()
            ->with([
                'cliente' => function ($query): void {
                    $query->orderBy('nominativo', 'asc');
                },
                'bibliotecario',
                'libro',
            ])
            ->select('prestito.*')
            ->orderBy('data_inizio_prestito', 'desc')
            ->get();

        return view('biblioteca.libri.prestiti.view', ['prestiti' => $prestiti,
            'msgSearch' => 'Tutti e prestiti attivi',
            'query' => '']);
    }

    public function search(Request $request)
    {
        $msgSearch = ' ';
        // se sto cercando il prestito di una persona redirect sul dettaglio della persona.
        if ($request->has('persona_id') and ! $request->has('note')) {
            Session::flash('clientePrestitiUrl', $request->fullUrl());
            $cliente = Persona::findOrFail($request->input('persona_id'));
            $prestitiAttivi = $cliente->prestiti()->where('in_prestito', 1)->orderBy('data_inizio_prestito')->get(); //Prestito::InPrestito()->where(["CLIENTE"=>$idCliente])->get();
            $prestitiRestituiti = $cliente->prestiti()->where('in_prestito', 0)->orderBy('data_fine_prestito')->get(); //Prestito::Restituiti()->where(["CLIENTE"=>$idCliente])->get();

            return view('biblioteca.libri.prestiti.cliente', compact('cliente', 'prestitiAttivi', 'prestitiRestituiti'));
        }

        $queryPrestiti = Prestito::where(function ($q) use ($request, &$msgSearch): void {
            if ($request->has('collocazione')) {
                $collocazione = $request->collocazione;
                $idLibri = Libro::where('collocazione', 'like', "$collocazione%")->pluck('id')->toArray();
                $q->whereIn('libro_id', $idLibri);
                // $q->where('libro_id', $libro->id);
                $msgSearch = $msgSearch.'Collocazione='.$collocazione;
            }
            if ($request->has('note')) {
                $note = $request->note;
                $q->where('note', 'like', "%$note%");
                $msgSearch = $msgSearch.' Note='.$note;
            }
            if ($request->has('titolo')) {
                $idLibri = Libro::withTrashed()->where('titolo', $request->titolo)->pluck('id')->toArray();
                $q->whereIn('libro_id', $idLibri);
                $msgSearch = $msgSearch." Titolo = $request->titolo";
            }
            if ($request->has('persona_id')) {
                $utente = $request->input('persona_id');
                $nomeUtente = Persona::findOrFail($utente)->nominativo;
                $q->where('cliente_id', $utente);
                $msgSearch = $msgSearch.' Cliente='.$nomeUtente;
            }
            if ($request->xSegnoInizioPrestito) {
                $inizioPrestito = $request->xInizioPrestito;
                $segnoPrestito = $request->xSegnoInizioPrestito;
                $q->where('data_inizio_prestito', $segnoPrestito, $inizioPrestito);
                $msgSearch = $msgSearch." Data Prestito $segnoPrestito $inizioPrestito";
            }
            if ($request->xSegnoFinePrestito) {
                $fineprestito = $request->xFinePrestito;
                $segnoFinePrestito = $request->xSegnoFinePrestito;
                $q->where('data_fine_prestito', $segnoFinePrestito, $fineprestito);
                $msgSearch = $msgSearch." Data restituzione $segnoFinePrestito $fineprestito";
            }
            if ($request->xIdBibliotecario) {
                $idBibliotecario = $request->xIdBibliotecario;
                $q->where('bibliotecario_id', $idBibliotecario);
                $bibliotecario = Persona::findOrFail($idBibliotecario)->nominativo;
                $msgSearch = $msgSearch." Bibliotecario: $bibliotecario ";
            }
        });

        $prestiti = $queryPrestiti->orderBy('data_inizio_prestito', 'DESC')->paginate(50);
        $query = $queryPrestiti->toSql();

        return view('biblioteca.libri.prestiti.view', ['prestiti' => $prestiti,
            'msgSearch' => $msgSearch,
            'query' => $query]);
    }

    public function show($idPrestito)
    {
        if (Session::has('clientePrestitiUrl')) { // contains the url of the detail of the utente
            Session::keep('clientePrestitiUrl');
        }
        $prestito = Prestito::findOrFail($idPrestito);

        return view('biblioteca.libri.prestiti.show', ['prestito' => $prestito]);
    }

    public function edit($idPrestito)
    {
        $prestito = Prestito::findOrFail($idPrestito);

        return view('biblioteca.libri.prestiti.edit', ['prestito' => $prestito]);
    }

    public function editConfirm(Request $request, $idPrestito)
    {
        $request->validate([
            'xDataRestituzione' => 'sometimes|nullable|date|after_or_equal:xDataPrenotazione',
        ], [
            'xDataRestituzione.after_or_equal' => 'La data di restituzione prestito deve essere maggiore o uguale alla data di inizio prestito',
        ]);

        $prestito = Prestito::findOrFail($idPrestito);

        $dataprenotazione = $request->xDataPrenotazione;
        $datarestituzione = $request->xDataRestituzione;
        $note = $request->input('xNote', null);

        $bibliotecario = Auth::user()->persona->id;

        $persona = Persona::findOrFail($request->persona_id);
        $prestito = Prestito::findOrFail($idPrestito);
        $prestito->update([
            'bibliotecario_id' => $bibliotecario,
            'data_fine_prestito' => $datarestituzione,
            'data_inizio_prestito' => $dataprenotazione,
            'note' => $note,
            'cliente_id' => $persona->id,
        ]);
        if ($prestito) {
            return redirect()->route('libri.prestiti')->withSuccess('Prestito modificato correttamente');
        }

        return redirect()->route('libri.prestiti')->withWarning('Nessuna modifica effettuata');

    }
}
