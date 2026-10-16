<?php

declare(strict_types=1);

namespace App\Scuola\Controllers;

use App\Biblioteca\Models\Autore;
use App\Biblioteca\Models\Editore;
use App\Biblioteca\Models\Libro;
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

    private const string DEFAULT_AUTORE = 'SCUOLA NOMADELFIA';

    private const string DEFAULT_EDITORE = 'SCUOLA NOMADELFIA';

    public function index(Request $request)
    {
        $order = $request->query('order', 'anno_scolastico');
        $filterYear = $request->string('year');
        $by = $request->query('by', 'DESC');
        $view = $request->input('view', 'cards');

        $years = Elaborato::query()
            ->selectRaw('anno_scolastico as year, count(*) as `count` ')
            ->groupByRaw('anno_scolastico')
            ->orderByRaw('anno_scolastico')
            ->get();

        $query = Elaborato::query()
            ->leftjoin('archivio_biblioteca.libro', 'elaborati.libro_id', '=', 'libro.id')
            ->select('elaborati.*', 'libro.autore')
            ->orderBy($order, $by)
            ->orderBy('elaborati.collocazione', 'desc');
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
            'collocazione' => ['required', 'regex:/^SP[A-Z]\d{3}$/', 'unique:db_biblioteca.libro,collocazione', 'unique:db_scuola.elaborati,collocazione'],
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
            'collocazione.required' => 'La collocazione è obbligatoria.',
            'collocazione.regex' => 'La collocazione deve essere nella forma SP + lettera + 3 numeri (es. SPA001).',
            'collocazione.unique' => 'La collocazione inserita esiste già.',
            'file.required' => 'Nessun file selezionato.',
            'anno_scolastico.required' => 'Anno scolastico è obbligatorio.',
            'titolo.required' => 'Il titolo è obbligatorio.',
            'studenti_ids.required' => 'Almeno un alunno è obbligatorio.',
        ]);
        $titolo = $request->input('titolo');
        $collocazione = $request->input('collocazione');
        $alunni = $request->input('studenti_ids');
        $coords = $request->input('coordinatori_ids');
        $as = AnnoScolastico::fromString($request->input('anno_scolastico'));

        $classi = $request->filled('classi') ? implode(',', $request->input('classi')) : '';
        $dimensione = $request->input('dimensione') ? Dimensione::fromString($request->input('dimensione'))->toString() : null;

        DB::connection('db_biblioteca')->transaction(function () use ($request, $titolo, $collocazione, $as, $alunni, $coords, $classi, $dimensione): void {
            DB::connection('db_scuola')->transaction(function () use ($request, $titolo, $collocazione, $as, $alunni, $coords, $classi, $dimensione): void {
                $file = $request->file('file');

                $destinationPath = $this->storeElaboratoFile($file, $as, $collocazione, $titolo);

                $libro = Libro::query()->create([
                    'titolo' => $titolo,
                    'collocazione' => $collocazione,
                    'classificazione_id' => null,
                    'dimensione' => $dimensione,
                    'note' => $request->input('note', null),
                    'tobe_printed' => 1,
                ]);
                $autore = Autore::query()->firstOrCreate(['autore' => self::DEFAULT_AUTORE, 'tipaut' => 'S']);
                $editore = Editore::query()->firstOrCreate(['editore' => self::DEFAULT_EDITORE, 'tipedi' => 'S']);
                $libro->autori()->sync([$autore->id]);
                $libro->editori()->sync([$editore->id]);

                $elaborato = Elaborato::query()->create(
                    attributes: [
                        'titolo' => $titolo,
                        'collocazione' => $collocazione,
                        'libro_id' => $libro->id,
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
                // The studenti/coordinatori relations run on the db_nomadelfia connection, which would wait
                // on the uncommitted elaborato row: insert the pivots through the db_scuola transaction instead.
                $scuola = DB::connection('db_scuola');
                $scuola->table('elaborati_studenti')->insert(
                    collect($alunni)->map(fn ($id): array => ['elaborato_id' => $elaborato->id, 'studente_id' => $id])->all()
                );
                $scuola->table('elaborati_coordinatori')->insert(
                    collect($coords ?? [])->map(fn ($id): array => ['elaborato_id' => $elaborato->id, 'coordinatore_id' => $id])->all()
                );
            });
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
