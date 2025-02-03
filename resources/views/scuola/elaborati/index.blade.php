@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Elaborati ", "subtitle" => $elaborati->count()])
    <div class="mb-3 d-flex justify-content-between">
        <a
            class="btn btn-secondary dropdown-toggle"
            href="#"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
        >
            Ordina per
        </a>
        <ul class="dropdown-menu">
            <li>
                <a
                    class="dropdown-item"
                    href="{{ route("scuola.elaborati.index", ["order" => "collocazione", "by" => "ASC"]) }}"
                >
                    Collocazione
                </a>
            </li>
            <li>
                <a
                    class="dropdown-item"
                    href="{{ route("scuola.elaborati.index", ["order" => "anno_scolastico", "by" => "ASC"]) }}"
                >
                    Anno
                </a>
            </li>
        </ul>

        <a
            class="btn btn-primary"
            href="{{ route("scuola.elaborati.create") }}"
            role="button"
        >
            Inserisci
        </a>
    </div>

    <!-- <div class="card">
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
    <!-- @if ($elaborato->autore)


                        <span class="alert alert-warning small">
                            {{ $elaborato->autore }}
                        </span>
                    @endif



                    <a
                        href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                        class="btn btn-primary"
                        role="button"
                    >
                        Dettaglio
                    </a>
                </li>
@empty


                <li class="list-group-item">Nessun elaborato disponibile.</li>
            @endforelse
        </ul>
    </div> -->

    <div class="row row-cols-1 row-cols-md-6 g-3">
        @foreach ($elaborati as $elaborato)
            <div class="col">
                <div class="card">
                    <img
                        src="/images/logo-noma.png"
                        class="card-img-top img-fluid"
                    />
                    <div class="card-body">
                        <h5 class="card-title">{{ $elaborato->titolo }}</h5>

                        <span class="badge bg-warning">
                            {{ $elaborato->anno_scolastico }}
                        </span>

                        @if ($elaborato->collocazione)
                            <span class="badge bg-primary">
                                {{ $elaborato->collocazione }}
                            </span>
                        @endif

                        @if ($elaborato->file_path)
                            <span class="badge bg-danger">pdf</span>
                        @endif

                        <a
                            href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                            class="stretched-link"
                        ></a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
