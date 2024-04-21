<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class EtichetteController
{
    public function view()
    {
        $libriTobePrinted = Libro::TobePrinted()->orderBY('COLLOCAZIONE')->get();

        return view('biblioteca.libri.etichette.view', ['libriTobePrinted' => $libriTobePrinted]);
    }

    public function preview(Request $request)
    {
        if ($request->has('idLibro')) {
            $libri = Libro::where('id', $request->idLibro)->get();
        } else {
            $libri = Libro::TobePrinted()->get();
        }

        return view('biblioteca.libri.etichette.printsingle', ['libri' => $libri]);
    }

    public function printToPdf(Request $request)
    {
        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $file_name = storage_path("etichette-$date.pdf");

        Browsershot::url(route('libri.etichette.preview', ['idLibro' => $request->get('idLibro')]))
            ->noSandbox()
            ->paperSize(config('etichette.dimensioni.larghezza'), config('etichette.dimensioni.altezza'))
            ->timeout(2000)
            ->savePdf($file_name);

        return response()->download($file_name)->deleteFileAfterSend();
    }

    public function addLibro($idLibro)
    {
        $res = Libro::find($idLibro)->update(['tobe_printed' => 1]);
        if ($res) {
            return redirect()->route('libro.dettaglio', ['idLibro' => $idLibro])->withSuccess('Libro aggiunto alla stampa delle etichette');
        } else {
            return redirect()->route('libro.dettaglio', ['idLibro' => $idLibro])->withError("Errore nell'operazione");
        }
    }

    public function removeLibro($idLibro)
    {
        $libro = Libro::find($idLibro);
        $res = $libro->update(['tobe_printed' => 0]);
        if ($res) {
            return redirect()->route('libri.etichette')->withSuccess("Libro $libro->collocazione, $libro->titolo eliminato dalla stampa delle etichette");
        } else {
            return redirect()->route('libri.etichette')->withError("Errore nell'operazione");
        }
    }

    public function etichetteFromToCollocazione(Request $request)
    {
        $from = $request->input('fromCollocazione');
        $to = $request->input('toCollocazione', $request->input('fromCollocazione'));
        if ($request->input('action') == 'add') {
            $count = Libro::whereBetween('collocazione', [$from, $to])->update(['tobe_printed' => 1]);

            return redirect()->route('libri.etichette')->withSuccess("$count etichette aggiunte alla stampa");
        } else {
            $count = Libro::whereBetween('collocazione', [$from, $to])->update(['tobe_printed' => 0]);

            return redirect()->route('libri.etichette')->withSuccess("$count etichette rimosse dalla stampa");
        }
    }

    public function removeAll(Request $request)
    {
        $res = Libro::toBePrinted()->update(['tobe_printed' => 0]);
        if ($res) {
            return redirect()->route('libri.etichette')->withSuccess("Tutte le $res etichette sono state eliminate.");
        } else {
            return redirect()->route('libri.etichette')->withError("Errore nell'operazione");
        }
    }
}
