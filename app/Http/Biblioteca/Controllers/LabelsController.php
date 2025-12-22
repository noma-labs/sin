<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Spatie\Browsershot\Browsershot;

final class LabelsController
{
    public function index()
    {
        $libriTobePrinted = Libro::TobePrinted()->orderBY('COLLOCAZIONE')->get();

        return view('biblioteca.books.labels.index', ['libriTobePrinted' => $libriTobePrinted]);
    }

    public function preview(Request $request)
    {
        if ($request->filled('idLibro')) {
            $libri = Libro::where('id', $request->idLibro)->get();
        } else {
            $libri = Libro::TobePrinted()->get();
        }

        return view('biblioteca.books.labels.printsingle', ['libri' => $libri]);
    }

    public function storeBook($idLibro)
    {
        $res = Libro::find($idLibro)->update(['tobe_printed' => 1]);
        if ($res) {
            return to_route('books.show', ['id' => $idLibro])->withSuccess('Libro aggiunto alla stampa delle etichette');
        }

        return to_route('books.show', ['id' => $idLibro])->withError("Errore nell'operazione");

    }

    public function removeLibro($idLibro)
    {
        $libro = Libro::find($idLibro);
        $res = $libro->update(['tobe_printed' => 0]);
        if ($res) {
            return to_route('books.labels')->withSuccess("Libro $libro->collocazione, $libro->titolo eliminato dalla stampa delle etichette");
        }

        return to_route('books.labels')->withError("Errore nell'operazione");

    }

    public function storeBatch(Request $request)
    {
        $from = $request->input('fromCollocazione');
        $to = $request->input('toCollocazione', $request->input('fromCollocazione'));
        if ($request->input('action') === 'add') {
            $count = Libro::whereBetween('collocazione', [$from, $to])->update(['tobe_printed' => 1]);

            return to_route('books.labels')->withSuccess("$count etichette aggiunte alla stampa");
        }
        $count = Libro::whereBetween('collocazione', [$from, $to])->update(['tobe_printed' => 0]);

        return to_route('books.labels')->withSuccess("$count etichette rimosse dalla stampa");

    }

    public function removeAll()
    {
        $res = Libro::toBePrinted()->update(['tobe_printed' => 0]);
        if ($res) {
            return to_route('books.labels')->withSuccess("Tutte le $res etichette sono state eliminate.");
        }

        return to_route('books.labels')->withError("Errore nell'operazione");
    }

    public function printToPdf(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $date = \Illuminate\Support\Facades\Date::now()->format('Y-m-d_H-i-s');
        $file_name = storage_path("etichette-$date.pdf");

        // Get the APP_URL from the configuration
        $appUrl = Config::get('app.url');

        // Construct the route path
        $routePath = route('books.labels.preview', ['idLibro' => $request->get('idLibro')], false);
        $url = $appUrl.$routePath;

        Browsershot::url($url)
            ->noSandbox()
            ->paperSize(29, 62) // 29mm x 62mm (max hight of the printer)
            ->timeout(2000)
            ->savePdf($file_name);

        return response()->download($file_name)->deleteFileAfterSend();
    }
}
