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

    <div class="row row-cols-1 row-cols-md-4 row-cols-lg-6 g-3">
        @foreach ($elaborati as $elaborato)
            <div class="col">
                <div class="card h-100">
                    <!-- <img
                        src="/images/logo-noma.png"
                        class="card-img-top img-fluid p-3"
                    /> -->
                    <div class="card-body">
                        <h5 class="card-title">{{ $elaborato->titolo }}</h5>

                        {{ strtolower($elaborato->note) }}

                        <!-- TODO: autore is taken from the "old" libro table and should be removed. It is only needed to have the old info for copying it into the new one -->
                        @if ($elaborato->autore)
                            <div class="alert alert-warning" role="alert">
                                {{ $elaborato->autore }}
                            </div>
                        @endif

                        <a
                            href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                            class="stretched-link"
                        ></a>
                    </div>
                    <div class="card-footer">
                        <span class="badge rounded-pill bg-secondary">
                            {{ $elaborato->anno_scolastico }}
                        </span>

                        @if ($elaborato->collocazione)
                            <span class="badge rounded-pill bg-primary">
                                {{ $elaborato->collocazione }}
                            </span>
                        @endif

                        @if ($elaborato->file_path)
                            <span class="badge rounded-pill bg-danger">
                                pdf
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
