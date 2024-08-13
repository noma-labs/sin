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

    <div class="card">
        <div class="card-header">Lista Elaborati</div>
        <ul class="list-group list-group-flush">
            @forelse ($elaborati as $elaborato)
                <li class="list-group-item">
                    <strong>{{ $elaborato->titolo }}</strong>
                    <span class="badge badge-primary">
                        {{ $elaborato->collocazione }}
                    </span>
                    <span class="badge badge-secondary">
                        {{ $elaborato->anno_scolastico }}
                    </span>

                    <span class="badge badge-secondary">
                        {{ strtolower($elaborato->rilegatura) }}
                    </span>

                    {{ strtolower($elaborato->note) }}

                    <span class="alert alert-warning small">
                        {{ $elaborato->autore }}
                    </span>
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
