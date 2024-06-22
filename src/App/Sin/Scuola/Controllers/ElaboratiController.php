<?php

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
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
        return view('scuola.elaborati.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ], [
            'file.required' => 'Nessun file selezionato.',
        ]);

        if (! $request->hasFile('file')) {
            dd('File not present');
        }

        $file = $request->file('file');

        $name = $file->hashName();
        // TODO: create a file system structure with year/month/YY_MM_<title>.EXT
        $uploaded = Storage::disk('local')->put("scuola/elaborati/{$name}", $file);

        Elaborato::query()->create(
            attributes: [
                'titolo' => "test",
                'autori' => "davide",
                'data' => "2024/12",
                'collocazione' => "ALDKA",
                'file_name' => $file->getClientOriginalName(),
            ]
         );
        // https://laravel-news.com/uploading-files-laravel

    }
}
