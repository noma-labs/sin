<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use Carbon\Carbon;
use Domain\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneAttuale;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PrintableExcelPopolazioneController
{
    public function store(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NUMERO ELENCO')
            ->setCellValue('B1', 'NOME')
            ->setCellValue('C1', 'COGNOME')
            ->setCellValue('D1', 'DATA NASCITA')
            ->setCellValue('E1', 'PROVINCIA NASCITA')
            ->setCellValue('F1', 'CODICE FISCALE')
            ->setCellValue('G1', 'SESSO')
            ->setCellValue('H1', 'POSIZIONE')
            ->setCellValue('I1', 'GRUPPO FAMILIARE')
            ->setCellValue('J1', 'FAMIGLIA')
            ->setCellValue('K1', 'AZIENDA');

        $population = PopolazioneAttuale::query()
            ->select('numero_elenco', 'nome', 'cognome', 'data_nascita', 'provincia_nascita', 'cf', 'sesso', 'posizione', 'gruppo', 'famiglia', 'azienda')
            ->get()
            ->toArray();
        $sheet->fromArray($population, null, 'A2');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $file_name = 'popolazione_nomadelfia_'.Carbon::now()->format('Y-m-d').'.xlsx';

        return new StreamedResponse(function () use ($writer): void {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="'.$file_name.'"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
