<?php

namespace App\Patente\Controllers;

use App\Patente\Models\CategoriaPatente;
use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\Cariche;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Spatie\Browsershot\Browsershot;
use Validator;

class PatenteController
{
    public function scadenze()
    {
        $patenti = Patente::with('persona')->SenzaCommisione()->InScadenza(config('patente.scadenze.patenti.inscadenza'))->orderBy('data_scadenza_patente')->get(); // 45 giorni
        $patentiScadute = Patente::with('persona')->SenzaCommisione()->Scadute(config('patente.scadenze.patenti.scadute'))->orderBy('data_scadenza_patente', 'asc')->get();

        $patentiCQCPersone = CQC::CQCPersone()->inScadenza(config('patente.scadenze.cqc.inscadenza'))->with('persona')->orderBy('data_scadenza', 'asc')->get();
        $patentiCQCPersoneScadute = CQC::CQCPersone()->scadute(config('patente.scadenze.cqc.scadute'))->with('persona')->orderBy('data_scadenza', 'asc')->get();
        $patentiCQCMerci = CQC::CQCMerci()->inScadenza(config('patente.scadenze.cqc.inscadenza'))->with('persona')->orderBy('data_scadenza', 'asc')->get();
        $patentiCQCMerciScadute = CQC::CQCMerci()->scadute(config('patente.scadenze.cqc.scadute'))->with('persona')->orderBy('data_scadenza', 'asc')->get();

        $patentiCommissione = Patente::with('persona')->ConCommisione()->InScadenza(config('patente.scadenze.commissione.inscadenza'))->orderBy('data_scadenza_patente')->get();
        $patentiCommisioneScadute = Patente::with('persona')->ConCommisione()->Scadute(config('patente.scadenze.commissione.scadute'))->orderBy('data_scadenza_patente', 'asc')->get();

        $patentiAll = Patente::sortable()->NonScadute()->with('persona')->orderBy('data_scadenza_patente', 'asc')->paginate(50);

        return view('patente.scadenze', compact('patenti',
            'patentiScadute',
            'patentiCQCPersone',
            'patentiCQCPersoneScadute',
            'patentiCQCMerci',
            'patentiCQCMerciScadute',
            'patentiCommissione',
            'patentiCommisioneScadute',
            'patentiAll'
        ));
    }

    public function elenchi()
    {
        return view('patente.elenchi');
    }

    public function esportaPatentiPdf()
    {
        dd('Esportazione non effettuata');
    }

    public function esportaPatentiExcel()
    {
        $data = Carbon::now();
        $name = "Patenti-$data.xlsx";

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'COGNOME')
            ->setCellValue('B1', 'NOME')
            ->setCellValue('C1', 'DATA NASCITA')
            ->setCellValue('D1', 'LUOGO NASCITA')
            ->setCellValue('E1', 'N PATENTE')
            ->setCellValue('F1', 'DATA RILASCIO')
            ->setCellValue('G1', 'RILASCIATA DA')
            ->setCellValue('H1', 'DATA SCADENZA')
            ->setCellValue('I1', 'CATEGORIE');
        // ->setCellValue('J1', 'STATO')
        // ->setCellValue('K1', 'NOTE');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        // $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        // $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray(['font' => ['bold' => true]]);

        $patenti = Patente::with('persona')->has('categorie')->get()->sortBy(function ($product) {
            return $product->persona->cognome;
        });

        $patenti = $patenti->map(function ($patente, $key) {
            return [$patente->persona->cognome,
                $patente->persona->nome,
                $patente->persona->data_nascita,
                $patente->persona->provincia_nascita,
                $patente->numero_patente,
                $patente->data_rilascio_patente,
                $patente->rilasciata_dal,
                $patente->data_scadenza_patente,
                $patente->categorieAsString(),
            ];    // $patente->stato,
            // str_replace(array("\r\n", "\r", "\n"), " ", $patente->note)); // reaplece \n\r with blank
        });

        $spreadsheet->getActiveSheet()->fromArray(
            $patenti->toArray(), //->toArray(),  // The data to set
            null, // Array values with this value will not be set
            'A2' // Top left coordinate of the worksheet range where  //    we want to set these values (default is A1)
            // true
        );
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$name.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function esportaCQCExcel()
    {
        $data = Carbon::now();
        $name = "cqc-$data.xlsx";
        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'COGNOME')
            ->setCellValue('B1', 'NOME')
            ->setCellValue('C1', 'DATA NASCITA')
            ->setCellValue('D1', 'LUOGO NASCITA')
            ->setCellValue('E1', 'N PATENTE')
            ->setCellValue('F1', 'DATA RILASCIO CQC PERSONE')
            ->setCellValue('G1', 'DATA SCADENZA CQC PERSONE')
            ->setCellValue('H1', 'DATA RILASCIO CQC MERCI')
            ->setCellValue('I1', 'DATA SCADENZA CQC MERCI');
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

        $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray(['font' => ['bold' => true]]);

        $cqcPersone = Patente::with('persona')->has('cqc')->get()->sortBy(function ($product) {
            return $product->persona->cognome;
        });

        $cqcPersone = $cqcPersone->map(function ($patente, $key) {
            return [$patente->persona->cognome,
                $patente->persona->nome,
                $patente->persona->data_nascita,
                $patente->persona->provincia_nascita,
                $patente->numero_patente,
                // $patente->data_rilascio_patente,
                // $patente->rilasciata_dal,
                $patente->cqcPersone() ? $patente->cqcPersone()->pivot->data_rilascio : '',
                $patente->cqcPersone() ? $patente->cqcPersone()->pivot->data_rilascio : '',
                $patente->cqcMerci() ? $patente->cqcMerci()->pivot->data_rilascio : '',
                $patente->cqcMerci() ? $patente->cqcMerci()->pivot->data_rilascio : '',
            ];
        });

        $spreadsheet->getActiveSheet()->fromArray(
            $cqcPersone->toArray(), //->toArray(),  // The data to set
            null, // Array values with this value will not be set
            'A2' // Top left coordinate of the worksheet range where  //    we want to set these values (default is A1)
            // true
        );

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToHeight(0);

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$name.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function stampaAutorizzatiPreview()
    {
        $persone = PopolazioneNomadelfia::take('id');
        $presidente = Cariche::GetAssociazionePresidente();
        $patentiAutorizzati = Patente::has('categorie')->get()
            ->sortBy(function ($product) {
                return $product->persona->nome;
            });

        return view('patente.elenchi.index', ['patentiAutorizzati' => $patentiAutorizzati, 'presidente' => $presidente]);

    }

    public function stampaAutorizzati()
    {
        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $file_name = storage_path("autorizzati-$date.pdf");

        Browsershot::url(route('patente.elenchi.autorizzati.esporta.preview'))
            ->noSandbox()
            ->format('A4')
            ->timeout(2000)
            ->margins(10, 20, 30, 40)
            ->savePdf($file_name);

        return response()->download($file_name)->deleteFileAfterSend();

    }

    public function autorizzatiEsportaExcel()
    {
        $data = Carbon::now();
        $name = "Conducenti autorizzati $data.xlsx";

        $spreadsheet = new Spreadsheet();
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'NOME')
            ->setCellValue('B1', 'COGNOME')
            ->setCellValue('C1', 'DATA NASCITA')
            ->setCellValue('D1', 'CATEGORIE');

        $patenti = Patente::with('persona')->has('categorie')->get()->sortBy(function ($product) {
            return $product->persona->nome;
        });

        //$patenti = Patente::with("persona")->has('categorie')->get()->map(function ($patente, $key) {
        //    return array($patente->persona->nome,$patente->persona->cognome, $patente->persona->data_nascita, $patente->categorieAsString());
        //  });

        $patenti = $patenti->map(function ($patente, $key) {
            return [$patente->persona->nome, $patente->persona->cognome, $patente->persona->data_nascita, $patente->categorieAsString()];
        });

        $spreadsheet->getActiveSheet()->fromArray(
            $patenti->toArray(), //->toArray(),  // The data to set
            null, // Array values with this value will not be set
            'A2' // Top left coordinate of the worksheet range where  //    we want to set these values (default is A1)
        );

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$name.'"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');

    }

    public function patente()
    {
        $categorie = CategoriaPatente::orderby('categoria')->get();
        $cqc = CQC::orderby('categoria')->get();

        return view('patente.search', compact('categorie', 'cqc'));
    }

    public function ricerca(Request $request)
    {
        // $persona = Persona::findorfail($request->input("persona_id"));
        if (! $request->except(['_token'])) {
            return redirect()->back()->withError('Nessun criterio di ricerca inserito.');
        }

        $msgSearch = ' ';
        $orderBy = 'numero_patente';
        $queryPatenti = Patente::where(function ($q) use ($request, &$msgSearch, &$orderBy) {
            if ($request->filled('persona_id')) {
                $persona = $request->persona_id;
                $q->where('persone_patenti.persona_id', $persona);
                $nome = Persona::findorfail($persona)->nominativo;
                $msgSearch = $msgSearch.'Persona='.$nome;

            }
            if ($request->filled('numero_patente')) {
                $numero_patente = $request->numero_patente;
                $q->where('numero_patente', 'LIKE', "$numero_patente%");
                $msgSearch = $msgSearch.' numero_patente='.$numero_patente;
            }
            if ($request->filled('criterio_data_rilascio') and $request->filled('data_rilascio')) {
                $q->where('data_rilascio_patente', $request->input('criterio_data_rilascio'), $request->input('data_rilascio'));
                $msgSearch = $msgSearch.' Data Rilascio'.$request->input('criterio_data_rilascio').$request->input('data_rilascio');
            }
            if ($request->filled('criterio_data_scadenza') and $request->filled('data_scadenza')) {
                $q->where('data_scadenza_patente', $request->input('criterio_data_scadenza'), $request->input('data_scadenza'));
                $orderBy = 'data_scadenza_patente';
                $msgSearch = $msgSearch.' Data scadenza'.$request->input('criterio_data_scadenza').$request->input('data_scadenza');
            }
            if ($request->filled('cqc_patente')) {
                $cqc = $request->cqc_patente;
                $q->whereHas('cqc', function ($q) use ($cqc, &$msgSearch, $request) {
                    $q->where('id', $cqc);
                    if ($request->filled('criterio_cqc_data_scadenza') and $request->filled('cqc_data_scadenza')) {
                        $q->where('data_scadenza', $request->input('criterio_cqc_data_scadenza'), $request->input('cqc_data_scadenza'));
                        $msgSearch = $msgSearch.' data scadenza '.$request->input('criterio_cqc_data_scadenza').$request->input('cqc_data_scadenza');
                    }
                });

                $nome = CQC::findorfail($cqc)->categoria;
                $msgSearch = $msgSearch.' cqc='.$nome;
            }
            if ($request->filled('categoria_patente')) {
                $categoria = $request->categoria_patente;
                $q->whereHas('categorie', function ($q) use ($categoria) {
                    $q->where('id', $categoria);
                });
                $nome = CategoriaPatente::findorfail($categoria)->categoria;
                $msgSearch = $msgSearch.' categoria='.$nome;
            }
        });
        //$msgSearch=$msgSearch."order by: $orderBy";
        $patenti = $queryPatenti->sortable($orderBy, 'asc')->paginate(25);

        $categorie = CategoriaPatente::orderby('categoria')->get();
        $cqc = CQC::orderby('categoria')->get();

        return view('patente.search', compact('patenti', 'categorie', 'cqc', 'msgSearch'));
    }

    public function elimina($id)
    {
        if (Patente::destroy($id)) {
            return redirect()->route('patente.scadenze')->withSuccess('Patente eliminata con successo.');
        } else {
            return redirect()->route('patente.scadenze')->withError('Errore: Patente non eliminata.');
        }

    }

    public function modifica($id)
    {
        $categorie = CategoriaPatente::all();
        $patente = Patente::find($id); //->where('numero_patente', '=', $id); //->get();

        return view('patente.modifica', compact('categorie', 'patente'));
    }

    private function validazioneRichiestaUpdate(Request $request)
    {
        $validRequest = Validator::make($request->all(), [
            'data_nascita' => 'required',
            'luogo_nascita' => 'required',
            'rilasciata_dal' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after_or_equal:data_rilascio_patente',
        ]);

        return $validRequest;
    }

    private function updatePatente(Request $request, $id)
    {
        $patente = Patente::find($id);
        $patente->update(['data_nascita' => request('data_nascita'),
            'luogo_nascita' => request('luogo_nascita'),
            'rilasciata_dal' => request('rilasciata_dal'),
            'data_rilascio_patente' => request('data_rilascio_patente'),
            'data_scadenza_patente' => request('data_scadenza_patente'),
            'note' => request('note'),
        ]);
    }

    private function addCategoriaUpdate(Request $request, $id)
    {
        if (request('nuova_categoria') != -1) {
            $patente = Patente::find($id);
            $categoria = CategoriaPatente::find(request('nuova_categoria'));
            $patente->categorie()->attach($categoria);
        }
    }

    public function confermaModifica(Request $request, $id)
    {
        $validatedData = $request->validate([
            'persona_id' => 'required',
            'numero_patente' => 'required',
            'rilasciata_dal' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after_or_equal:data_rilascio_patente',
        ], [
            'persona_id.required' => 'La persona è obbligatoria.',
            'numero_patente.required' => 'Il numero patente è obbligatorio.',
            'rilasciata_dal.required' => "L'ente he ha rilasciato è obbligatorio.",
            'data_rilascio_patente.required' => 'La data di rilascio è obbligatoria..',
            'data_scadenza_patente.required' => 'La data di scadenza è obbligatoria.',
        ]);

        $patente = Patente::find($id);
        $res = $patente->update(['rilasciata_dal' => request('rilasciata_dal'),
            'numero_patente' => request('numero_patente'),
            'data_rilascio_patente' => request('data_rilascio_patente'),
            'data_scadenza_patente' => request('data_scadenza_patente'),
            'note' => request('note'),
        ]);
        // $this->addCategoriaUpdate($request,$id);
        if ($res == 1) {
            return redirect(route('patente.ricerca'))->withSuccess("Patente $patente->numero_patente modificata con successo");
        } else {
            return redirect(route('patente.ricerca'))->withErroe("Errore nell'aggiornament della patente $patente->numero_patente");
        }

    }

    public function inserimento()
    {
        $categorie = CategoriaPatente::all();
        $persone = Persona::all();

        return view('patente.inserimento', compact('categorie', 'persone'));
    }

    private function creaPatente(Request $request)
    {
        Patente::create([
            'persona_id' => request('persona'),
            'numero_patente' => request('numero_patente'),
            'data_nascita' => request('data_nascita'),
            'luogo_nascita' => request('luogo_nascita'),
            'rilasciata_dal' => request('rilasciata_dal'),
            'data_rilascio_patente' => request('data_rilascio_patente'),
            'data_scadenza_patente' => request('data_scadenza_patente'),
            'note' => request('note'),
        ]);
    }

    private function validazioneRichiestaInserimento(Request $request)
    {
        $validRequest = Validator::make($request->all(), [
            'persona' => 'required',
            'numero_patente' => 'required',
            'data_nascita' => 'required',
            'luogo_nascita' => 'required',
            'rilasciata_dal' => 'required',
            'data_rilascio_patente' => 'required|date',
            'data_scadenza_patente' => 'required|date|after_or_equal:data_rilascio_patente',
        ]);

        return $validRequest;
    }

    public function confermaInserimento(Request $request)
    {
        dd($request->input());
        $validRequest = $this->validazioneRichiestaInserimento($request);
        if ($validRequest->fails()) {
            return redirect(route('patente.index'))->withErrors($validRequest)->withInput();
        }
        $this->creaPatente($request);
        $patente = Patente::find(request('numero_patente'));
        $categoria = CategoriaPatente::find(request('categoria_patente'));
        $patente->categorie()->attach($categoria);

        //$viewData = Patente::with(['persone', 'categorie'])->orderBy("persona_id")->paginate(10);
        return redirect(route('patente.index'))->withSuccess('La patente numero:'.request('numero_patente').' è stata creata con successo');
    }
}
