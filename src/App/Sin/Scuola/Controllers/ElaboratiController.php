<?php

namespace App\Scuola\Controllers;

use App\Scuola\Models\Elaborato;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $titolo = $request->input('titolo');
        $titleSlug = Str::slug($titolo);

        $annoScolastico = $request->input('anno_scolastico');
        $as = Str::of($annoScolastico)->explode('/');
        $year = $as[1];
        $date = Carbon::now()->year($year)->month(6)->endOfMonth();
        $datePath = $date->format('Y-m-d');

        $file = $request->file('file');

        $filePath = "{$year}/{$datePath}_{$titleSlug}";
        $fileName = "{$datePath}_{$titleSlug}.{$file->getClientOriginalExtension()}";

        $storagePath = $file->storeAs($filePath, $fileName, 'scuola');

        if (! $storagePath) {
            return redirect()->back()->withError('Errore durante il caricamento del file.');
        }

        Elaborato::query()->create(
            attributes: [
                'titolo' => $titolo,
                'anno_scolastico' => $annoScolastico,
                'classi' => implode(',', $request->input('classi')),
                'file_path' => $storagePath,
                'file_mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
                'file_hash' => hash_file('sha256', $file->getPathname()),            ]
        );
    }

    public function show($id)
    {
        $elaborato = Elaborato::query()->findOrFail($id);

        $preview = asset('scuola/'. $elaborato->file_path);


        return view('scuola.elaborati.show', [
            'elaborato' => $elaborato,
            'preview' => $preview,
        ]);
    }

    public function download($id)
    {
        $elaborato = Elaborato::findOrFail($id);
        $filePath = $elaborato->file_path;

        if (!Storage::disk('scuola')->exists($filePath)) {
            abort(404);
        }

        $fileName = basename($filePath); // Get the base name in case you want to customize the download file name

        return Storage::disk('scuola')->download($filePath, $fileName);
    }

}
