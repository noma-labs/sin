<?php

declare(strict_types=1);

namespace App\Patente\Controllers;

use App\Patente\Models\Patente;
use Carbon;
use App\Nomadelfia\Persona\Models\Persona;
use App\Nomadelfia\PopolazioneNomadelfia\Models\Cariche;
use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneNomadelfia;
use Illuminate\Support\Facades\Config;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Spatie\Browsershot\Browsershot;

final class PatenteElenchiController
{
    public function index()
    {
        return view('patente.elenchi');
    }

    public function esportaPatentiPdf(): never
    {
        dd('Esportazione non effettuata');
    }

    public function esportaPatentiExcel(): void
    {
        $data = Carbon::now();
        $name = "Patenti-$data.xlsx";

        $spreadsheet = new Spreadsheet;
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

        $patenti = Patente::with('persona')->has('categorie')->get()->sortBy(fn ($product) => $product->persona->cognome);

        $patenti = $patenti->map(function ($patente, $key): array {
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
            $patenti->toArray(), // ->toArray(),  // The data to set
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

    public function esportaCQCExcel(): void
    {
        $data = Carbon::now();
        $name = "cqc-$data.xlsx";
        $spreadsheet = new Spreadsheet;
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

        $cqcPersone = Patente::with('persona')->has('cqc')->get()->sortBy(fn ($product) => $product->persona->cognome);

        $cqcPersone = $cqcPersone->map(fn ($patente, $key): array => [$patente->persona->cognome,
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
        ]);

        $spreadsheet->getActiveSheet()->fromArray(
            $cqcPersone->toArray(), // ->toArray(),  // The data to set
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
        PopolazioneNomadelfia::take('id');
        $presidente = Cariche::GetAssociazionePresidente();
        $patentiAutorizzati = Patente::has('categorie')->get()
            ->sortBy(fn ($product) => $product->persona->nome);

        return view('patente.elenchi.index', ['patentiAutorizzati' => $patentiAutorizzati, 'presidente' => $presidente]);

    }

    public function stampaAutorizzati(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $date = Carbon::now()->format('Y-m-d_H-i-s');
        $file_name = storage_path("autorizzati-$date.pdf");

        // Get the APP_URL from the configuration
        $appUrl = Config::get('app.url');

        // Construct the route path
        $routePath = route('patente.elenchi.autorizzati.esporta.preview', [], false);
        $url = $appUrl.$routePath;

        Browsershot::url($url)
            ->noSandbox()
            ->format('A4')
            ->timeout(2000)
            ->margins(10, 20, 30, 40)
            ->savePdf($file_name);

        return response()->download($file_name)->deleteFileAfterSend();

    }

    public function autorizzatiEsportaExcel(): void
    {
        $data = Carbon::now();
        $name = "Conducenti autorizzati $data.xlsx";

        $spreadsheet = new Spreadsheet;
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'NOME')
            ->setCellValue('B1', 'COGNOME')
            ->setCellValue('C1', 'DATA NASCITA')
            ->setCellValue('D1', 'CATEGORIE');

        $patenti = Patente::with('persona')->has('categorie')->get()->sortBy(fn ($product) => $product->persona->nome);

        // $patenti = Patente::with("persona")->has('categorie')->get()->map(function ($patente, $key) {
        //    return array($patente->persona->nome,$patente->persona->cognome, $patente->persona->data_nascita, $patente->categorieAsString());
        //  });

        $patenti = $patenti->map(fn ($patente, $key): array => [$patente->persona->nome, $patente->persona->cognome, $patente->persona->data_nascita, $patente->categorieAsString()]);

        $spreadsheet->getActiveSheet()->fromArray(
            $patenti->toArray(), // ->toArray(),  // The data to set
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
}
