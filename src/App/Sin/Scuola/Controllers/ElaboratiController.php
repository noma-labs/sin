<?php

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ElaboratiController
{
    public function index()
    {
        return view('scuola.elaborati.index');
    }

    public function create()
    {
        $now = Carbon::now();
        $annoScolastico = $now->year.'/'.($now->year + 1);

        return view('scuola.elaborati.create', [
            'annoScolastico' => $annoScolastico,
            'classi' => ['prescuola', '1 elementare', '2 elementare', '3 elementare', '4 elementare', '5 elementare', '1 media', '2 media', '3 media', '1 superiore', '2 superiore', '3 superiore', '4 superiore', '5 superiore'],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'titolo' => 'required',
            'anno_scolastico' => 'required',
        ], [
            'file.required' => 'Nessun file selezionato.',
            'anno_scolastico.required' => 'Anno scolastico è obbligatorio.',
            'titolo.required' => 'Il titolo è obbligatorio.',
        ]);

        if (! $request->hasFile('file')) {
            dd('File not present');
        }

        dd("Implement store method");

        $file = $request->file('file');

        // TODO: create a file system structure with year/month/YYYY_MM_DD <title>.ext
        $uploaded = Storage::disk('local')->put("scuola/elaborati/{$name}", $file);

        Elaborato::query()->create(
            attributes: [
                'titolo' => 'test',
                'autori' => 'davide',
                'data' => '2024/12',
                'collocazione' => 'ALDKA',
                'file_name' => $file->getClientOriginalName(),
            ]
        );
        // https://laravel-news.com/uploading-files-laravel

    }
}
