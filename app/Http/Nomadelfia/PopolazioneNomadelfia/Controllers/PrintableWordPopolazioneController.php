<?php

declare(strict_types=1);

namespace App\Nomadelfia\PopolazioneNomadelfia\Controllers;

use App\Nomadelfia\PopolazioneNomadelfia\Actions\ExportPopolazioneToWordAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

final class PrintableWordPopolazioneController
{
    public function index(Request $request)
    {
        $elenchi = collect($request->elenchi);
        $action = new ExportPopolazioneToWordAction;
        $word = $action->execute($elenchi);

        $objWriter = IOFactory::createWriter($word, 'Word2007');
        $data = Carbon::now()->toDatestring();
        $file_name = "popolazione-$data.docx";

        $objWriter->save(storage_path($file_name));

        return response()->download(storage_path($file_name));
    }
}
