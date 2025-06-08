<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\PopolazioneNomadelfia\Models\PopolazioneAttuale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PrintableExcelPopolazioneController
{
    public function __invoke(Request $request): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'NUMERO ELENCO')
            ->setCellValue('B1', 'NOMINATIVO')
            ->setCellValue('C1', 'NOME')
            ->setCellValue('D1', 'COGNOME')
            ->setCellValue('E1', 'DATA NASCITA')
            ->setCellValue('F1', 'PROVINCIA NASCITA')
            ->setCellValue('G1', 'CODICE FISCALE')
            ->setCellValue('H1', 'SESSO')
            ->setCellValue('I1', 'POSIZIONE')
            ->setCellValue('K1', 'GRUPPO FAMILIARE')
            ->setCellValue('K1', 'FAMIGLIA')
            ->setCellValue('L1', 'AZIENDA');

        $query = PopolazioneAttuale::query()->select('numero_elenco', 'nominativo', 'nome', 'cognome', 'data_nascita', 'provincia_nascita', 'cf', 'sesso', 'posizione', 'gruppo', 'famiglia', 'azienda');

        $ageFilter = $request->string('age');
        $positionFilter = $request->string('position');
        $sexFilter = $request->string('sex');
        if (! $ageFilter->isEmpty()) {
            match ($ageFilter->toString()) {
                'overage' => $query->overage(),
                'underage' => $query->underage(),
                default => null,
            };
        }
        if (! $positionFilter->isEmpty()) {
            match ($positionFilter->toString()) {
                'effettivo' => $query->effettivo(),
                'postulante' => $query->postulante(),
                'ospite' => $query->ospite(),
                'figlio' => $query->figlio(),
                default => null,
            };
        }
        if (! $sexFilter->isEmpty()) {
            match ($sexFilter->toString()) {
                'male' => $query->male(),
                'female' => $query->female(),
                default => null,
            };
        }

        $population = $query->get()
            ->map(fn ($item) => [
                'numero_elenco' => Str::upper($item['numero_elenco']),
                'nominativo' => Str::title($item['nominativo']),
                'nome' => Str::title($item['nome']),
                'cognome' => Str::title($item['cognome']),
                'data_nascita' => $item['data_nascita'],
                'provincia_nascita' => Str::title($item['provincia_nascita']),
                'cf' => Str::upper($item['cf']),
                'sesso' => Str::title($item['sesso']),
                'posizione' => Str::title($item['posizione']),
                'gruppo' => Str::title($item['gruppo']),
                'famiglia' => Str::title($item['famiglia']),
                'azienda' => Str::title($item['azienda']),
            ])
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
