<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use App\Core\Controllers\BaseController as CoreBaseController;
use Carbon\Carbon;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use SnappyPdf;

class EtichetteController extends CoreBaseController
{
    public function __construct()
    {
        //  $this->middleware('auth',['only'=>['edit','editConfirm','insert','insertConfirm']]);
        //$this->middleware('auth');

    }

    public function downloadExcel()
    {
        $data = Carbon::now();
        Excel::create("etichette-$data", function ($excel) {
            $excel->sheet('Etichette', function ($sheet) {
                $collocazioni = Libro::TobePrinted()->pluck('collocazione')->map(function ($item, $key) {
                    return Collection::wrap($item);
                });
                $sheet->fromArray($collocazioni->toArray(), null, 'A4');
            });

        })->download('csv');

        return redirect()->route('libri.etichette')->withSuccess('Csv scricato correttamente');
    }

    public function view()
    {
        $libriTobePrinted = Libro::TobePrinted()->orderBY('COLLOCAZIONE')->get();

        return view('biblioteca.libri.etichette.view', ['libriTobePrinted' => $libriTobePrinted]);
    }

    public function preview()
    {
        $etichette = $libriTobePrinted = Libro::TobePrinted()->get();

        return view('biblioteca.libri.etichette.printsingle', ['etichette' => $etichette]);
    }

    public function printToPdf()
    {
        $etichette = Libro::TobePrinted()->get();

        return $this->generateEtichette($etichette);
    }

    public static function stampaSingle(Libro $libro)
    {
        return self::generateEtichette(collect([$libro]));
    }

    public static function generateEtichette($etichette)
    {
        $pdf = SnappyPdf::loadView('biblioteca.libri.etichette.printsingle', ['etichette' => $etichette])
            ->setOption('page-width', config('etichette.dimensioni.larghezza'))
            ->setOption('page-height', config('etichette.dimensioni.altezza'))
            ->setOption('margin-bottom', '0mm')
            ->setOption('margin-top', '0mm')
            ->setOption('margin-right', '0mm')
            ->setOption('margin-left', '0mm');
        $data = Carbon::now();

        return $pdf->setPaper('a4')->setOrientation('portrait')->download("etichette-$data.pdf");
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
