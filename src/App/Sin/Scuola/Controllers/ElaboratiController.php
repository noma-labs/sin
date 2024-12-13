<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\DataTransferObjects\Dimensione;
use App\Scuola\Models\Elaborato;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ElaboratiController
{
    public function index(Request $request)
    {
        $order = $request->query('order', 'anno_scolastico');
        $by = $request->query('by', 'DESC');
        $elaborati = Elaborato::query()
            ->leftjoin('archivio_biblioteca.libro', 'elaborati.libro_id', '=', 'libro.id')
            ->select('elaborati.*', 'libro.autore')
            ->orderBy($order, $by)
            ->orderBy('created_at', 'DESC')
            ->get();

        return view('scuola.elaborati.index', [
            'elaborati' => $elaborati,
        ]);
    }

    public function create()
    {
        $now = Carbon::now();
        $annoScolastico = $now->year.'/'.($now->year + 1);

        return view('scuola.elaborati.create', [
            'annoScolastico' => $annoScolastico,
            'classi' => ['personale', 'prescuola', 'medie', 'superiori', 'tutti i cicli', '1 elementare', '2 elementare', '3 elementare', '4 elementare', '5 elementare', '1 media', '2 media', '3 media', '1 superiore', '2 superiore', '3 superiore', '4 superiore', '5 superiore'],
            'rilegature' => [
                'Altro',
                'Anelli',
                'Brossura (Copertina Flessibile)',
                'Cartonata (Copertina Rigida)',
                'Filo Refe',
                'Punto Metallico (Spillatura)',
                'Spirale',
                'Termica',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required',
            'titolo' => 'required',
            'anno_scolastico' => 'required',
            'studenti_ids' => 'required',
        ], [
            'file.required' => 'Nessun file selezionato.',
            'anno_scolastico.required' => 'Anno scolastico è obbligatorio.',
            'titolo.required' => 'Il titolo è obbligatorio.',
            'studenti_ids.required' => 'Almeno un alunno è obbligatorio.',
        ]);

        $titolo = $request->input('titolo');
        $alunni = $request->input('studenti_ids');
        $coords = $request->input('coordinatori_ids');
        $as = AnnoScolastico::fromString($request->input('anno_scolastico'));
        $file = $request->file('file');

        $titleSlug = Str::slug($titolo);
        $collocazione = $request->input('collocazione', '');

        $filePath = "{$as->endYear}/{$collocazione}_{$titleSlug}";
        $fileName = "{$collocazione}_{$titleSlug}.{$file->getClientOriginalExtension()}";

        $storagePath = $file->storeAs($filePath, $fileName, 'scuola');

        if (! $storagePath) {
            return redirect()->back()->withError('Errore durante il caricamento del file.');
        }

        $classi = $request->filled('classi') ? implode(',', $request->input('classi')) : '';
        $dimensione = $request->input('dimensione') ? Dimensione::fromString($request->input('dimensione'))->toString() : null;

        DB::Transaction(function () use ($request, $titolo, $as, $alunni, $coords, $storagePath, $file, $classi, $dimensione): void {
            $elaborato = Elaborato::query()->create(
                attributes: [
                    'titolo' => $titolo,
                    'anno_scolastico' => $as->toString(),
                    'classi' => $classi,
                    'dimensione' => $dimensione,
                    'rilegatura' => $request->input('rilegatura'),
                    'note' => $request->input('note', null),
                    'file_path' => $storagePath,
                    'file_mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'file_hash' => hash_file('sha256', $file->getPathname()),
                ]
            );
            $elaborato->studenti()->sync($alunni);
            $elaborato->coordinatori()->sync($coords);
        });

        return redirect()->route('scuola.elaborati.index')->withSuccess('Elaborato caricato con successo.');
    }

    public function show($id)
    {
        $elaborato = Elaborato::with('studenti')->findOrFail($id);

        return view('scuola.elaborati.show', ['elaborato' => $elaborato]);
    }

    public function edit($id)
    {
        $elaborato = Elaborato::with('studenti', 'coordinatori')->findOrFail($id);

        return view('scuola.elaborati.edit', [
            'elaborato' => $elaborato,
            'classi' => ['personale', 'prescuola', 'medie', 'superiori', 'tutti i cicli', '1 elementare', '2 elementare', '3 elementare', '4 elementare', '5 elementare', '1 media', '2 media', '3 media', '1 superiore', '2 superiore', '3 superiore', '4 superiore', '5 superiore'],
            'rilegature' => [
                'Altro',
                'Anelli',
                'Brossura (Copertina Flessibile)',
                'Cartonata (Copertina Rigida)',
                'Filo Refe',
                'Punto Metallico (Spillatura)',
                'Spirale',
                'Termica',
            ],
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'titolo' => 'required',
            'anno_scolastico' => 'required',
        ], [
            'anno_scolastico.required' => 'Anno scolastico è obbligatorio.',
            'titolo.required' => 'Il titolo è obbligatorio.',
        ]);
        $elaborato = Elaborato::findOrFail($id);

        DB::Transaction(function () use ($request, $elaborato): void {
            $dim = Dimensione::fromString($request->input('dimensione'));

            $elaborato->titolo = $request->input('titolo');
            $elaborato->anno_scolastico = str(AnnoScolastico::fromString($request->input('anno_scolastico')));
            $elaborato->note = $request->input('note');
            $elaborato->dimensione = $dim ? $dim->toString() : null;
            $elaborato->rilegatura = $request->input('rilegatura');

            $elaborato->classi = $request->filled('classi') ? implode(',', $request->input('classi')) : '';
            $elaborato->save();

            $alunni = $request->input('studenti_ids');
            $elaborato->studenti()->sync($alunni);

            $coordinatori = $request->input('coordinatori_ids');
            $elaborato->coordinatori()->sync($coordinatori);
        });

        return redirect()->route('scuola.elaborati.show', $elaborato->id)
            ->with('success', 'Elaborato aggiornato con successo.');
    }

    public function preview($id)
    {
        $elaborato = Elaborato::findOrFail($id);
        $filePath = $elaborato->file_path;

        if (! Storage::disk('scuola')->exists($filePath)) {
            abort(404);
        }

        $fileContent = Storage::disk('scuola')->get($filePath);
        $mimeType = Storage::disk('scuola')->mimeType($filePath);

        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }

    public function download($id)
    {
        $elaborato = Elaborato::findOrFail($id);
        $filePath = $elaborato->file_path;

        if (! Storage::disk('scuola')->exists($filePath)) {
            abort(404);
        }

        $fileName = basename($filePath); // Get the base name in case you want to customize the download file name

        return Storage::disk('scuola')->download($filePath, $fileName);
    }
}
