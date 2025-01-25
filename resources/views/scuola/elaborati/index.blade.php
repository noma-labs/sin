@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Elaborati"])

    <a
        class="btn btn-primary my-2"
        href="{{ route("scuola.elaborati.create") }}"
        role="button"
    >
        Inserisci
    </a>

    <a
        class="btn btn-secondary my-2"
        href="{{ route("scuola.elaborati.index", ["order" => "collocazione", "by" => "ASC"]) }}"
        role="button"
    >
        Ordina per Collocazione
    </a>
    <a
        class="btn btn-secondary my-2"
        href="{{ route("scuola.elaborati.index", ["order" => "anno_scolastico", "by" => "ASC"]) }}"
        role="button"
    >
        Ordina per Anno
    </a>

    <div class="card">
        <div class="card-header">
            Lista Elaborati
            <span class="fw-bold">({{ $elaborati->count() }})</span>
        </div>
        <ul class="list-group list-group-flush">
            @forelse ($elaborati as $elaborato)
                <li class="list-group-item">
                    <span class="badge bg-warning">
                        {{ $elaborato->anno_scolastico }}
                    </span>

                    <span class="badge bg-primary">
                        {{ $elaborato->collocazione }}
                    </span>

                    <strong>{{ $elaborato->titolo }}</strong>

                    @if ($elaborato->file_path)
                        <span class="badge bg-danger">pdf</span>
                    @endif

                    <span class="badge bg-secondary">
                        {{ strtolower($elaborato->rilegatura) }}
                    </span>

                    {{ strtolower($elaborato->note) }}

                    <!-- TODO: autore is taken from the "old" libro table and should be removed. It is only needed to have the old info for copying it into the new one -->
                    @if ($elaborato->autore)
                        <span class="alert alert-warning small">
                            {{ $elaborato->autore }}
                        </span>
                    @endif

                    <a
                        href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                        class="btn btn-sm btn-secondary float-right"
                    >
                        Dettaglio
                    </a>
                </li>
            @empty
                <li class="list-group-item">Nessun elaborato disponibile.</li>
            @endforelse
        </ul>
    </div>
@endsection
