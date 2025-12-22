<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\DataTransferObjects\Dimensione;
use App\Scuola\Exceptions\BadDimensionException;
use App\Scuola\Models\Elaborato;
use App\Scuola\Traits\StoresElaboratoFile;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class ElaboratiController
{
    use StoresElaboratoFile;

    public function index(Request $request)
    {
        $order = $request->query('order', 'anno_scolastico');
        $filterYear = $request->string('year');
        $by = $request->query('by', 'DESC');
        $view = $request->get('view', 'cards');

        $years = Elaborato::query()
            ->selectRaw('anno_scolastico as year, count(*) as `count` ')
            ->groupByRaw('anno_scolastico')
            ->orderByRaw('anno_scolastico')
            ->get();

        $query = Elaborato::query()
            ->leftjoin('archivio_biblioteca.libro', 'elaborati.libro_id', '=', 'libro.id')
            ->select('elaborati.*', 'libro.autore')
            ->orderBy($order, $by)
            ->orderBy('elaborati.collocazione', 'DESC');
        if (! $filterYear->isEmpty()) {
            $query->where('anno_scolastico', $filterYear);
        }
        $elaborati = $query->get();
        $total = Elaborato::query()->count();

        return view('scuola.elaborati.index', compact('elaborati', 'view', 'years', 'total'));
    }

    public function create()
    {
        $now = \Illuminate\Support\Facades\Date::now();
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
            'file' => ['required'],
            'titolo' => ['required'],
            'anno_scolastico' => ['required'],
            'studenti_ids' => ['required'],
            'dimensione' => function (string $attribute, mixed $value, Closure $fail): void {
                try {
                    if (! $value) {
                        return;
                    }
                    Dimensione::fromString($value);
                } catch (BadDimensionException $e) {
                    $fail($e->getMessage());
                }

            },
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

        $classi = $request->filled('classi') ? implode(',', $request->input('classi')) : '';
        $dimensione = $request->input('dimensione') ? Dimensione::fromString($request->input('dimensione'))->toString() : null;

        DB::Transaction(function () use ($request, $titolo, $as, $alunni, $coords, $classi, $dimensione): void {
            $file = $request->file('file');

            // FIXME: collocazione is empty !!!
            $destinationPath = $this->storeElaboratoFile($file, $as, '', $titolo);

            $elaborato = Elaborato::query()->create(
                attributes: [
                    'titolo' => $titolo,
                    'anno_scolastico' => $as,
                    'classi' => $classi,
                    'dimensione' => $dimensione,
                    'rilegatura' => $request->input('rilegatura'),
                    'note' => $request->input('note', null),
                    'file_path' => $destinationPath,
                    'file_mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'file_hash' => hash_file('sha256', $file->getPathname()),
                ]
            );
            $elaborato->studenti()->sync($alunni);
            $elaborato->coordinatori()->sync($coords);
        });

        return to_route('scuola.elaborati.index')->withSuccess('Elaborato caricato con successo.');
    }

    public function show($id)
    {
        $elaborato = Elaborato::with('studenti')
            ->leftjoin('archivio_biblioteca.libro', 'elaborati.libro_id', '=', 'libro.id')
            ->select('elaborati.*', 'libro.autore')
            ->where('elaborati.id', $id)
            ->first();

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
            'titolo' => ['required'],
            'anno_scolastico' => ['required'],
            'dimensione' => function (string $attribute, mixed $value, Closure $fail): void {
                try {
                    if (! $value) {
                        return;
                    }
                    Dimensione::fromString($value)->toString();
                } catch (BadDimensionException $e) {
                    $fail($e->getMessage());
                }

            },
        ], [
            'anno_scolastico.required' => 'Anno scolastico è obbligatorio.',
            'titolo.required' => 'Il titolo è obbligatorio.',
        ]);
        $elaborato = Elaborato::findOrFail($id);

        DB::Transaction(function () use ($request, $elaborato): void {
            $dim = $request->input('dimensione') ? Dimensione::fromString($request->input('dimensione'))->toString() : null;

            $elaborato->titolo = $request->input('titolo');
            $elaborato->anno_scolastico = str(AnnoScolastico::fromString($request->input('anno_scolastico')));
            $elaborato->note = $request->input('note');
            $elaborato->dimensione = $dim;
            $elaborato->rilegatura = $request->input('rilegatura');

            $elaborato->classi = $request->filled('classi') ? implode(',', $request->input('classi')) : '';
            $elaborato->save();

            $alunni = $request->input('studenti_ids');
            $elaborato->studenti()->sync($alunni);

            $coordinatori = $request->input('coordinatori_ids');
            $elaborato->coordinatori()->sync($coordinatori);
        });

        return to_route('scuola.elaborati.show', $elaborato->id)
            ->with('success', 'Elaborato aggiornato con successo.');
    }

    public function preview($id)
    {
        $elaborato = Elaborato::findOrFail($id);
        $filePath = $elaborato->file_path;

        if (! Storage::disk('media_originals')->exists($filePath)) {
            abort(404);
        }

        $fileContent = Storage::disk('media_originals')->get($filePath);
        $mimeType = Storage::disk('media_originals')->mimeType($filePath);

        return response($fileContent, 200)->header('Content-Type', $mimeType);
    }

    public function download($id)
    {
        $elaborato = Elaborato::findOrFail($id);
        $filePath = $elaborato->file_path;

        if (! Storage::disk('media_originals')->exists($filePath)) {
            abort(404);
        }

        $fileName = basename((string) $filePath); // Get the base name in case you want to customize the download file name

        return Storage::disk('media_originals')->download($filePath, $fileName);
    }
}
