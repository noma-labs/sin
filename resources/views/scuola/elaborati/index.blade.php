@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Elaborati ", "subtitle" => $elaborati->count()])
    <div class="mb-3 d-flex justify-content-between">
        <div>
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
                        href="{{ route("scuola.elaborati.index", ["order" => "collocazione", "by" => "ASC", "view" => $view]) }}"
                    >
                        Collocazione
                    </a>
                </li>
                <li>
                    <a
                        class="dropdown-item"
                        href="{{ route("scuola.elaborati.index", ["order" => "anno_scolastico", "by" => "ASC", "view" => $view]) }}"
                    >
                        Anno
                    </a>
                </li>
            </ul>
            <a
                class="btn btn-secondary dropdown-toggle"
                href="#"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                Vista
            </a>
            <ul class="dropdown-menu">
                <li>
                    <a
                        class="dropdown-item"
                        href="{{ route("scuola.elaborati.index", ["view" => "table"]) }}"
                    >
                        Tabella
                    </a>
                </li>
                <li>
                    <a
                        class="dropdown-item"
                        href="{{ route("scuola.elaborati.index", ["view" => "cards"]) }}"
                    >
                        Griglia
                    </a>
                </li>
            </ul>
        </div>

        <a
            class="btn btn-primary"
            href="{{ route("scuola.elaborati.create") }}"
            role="button"
        >
            Inserisci
        </a>
    </div>
    @if ($view === "table")
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr class="table-warning">
                        <th scope="col">#</th>
                        <th scope="col">Titolo</th>
                        <th scope="col">Anno Scolastico</th>
                        <th scope="col">Collocazione</th>
                        <th scope="col">Type</th>
                        <th scope="col">Dimensione</th>
                        <th scope="col">Note</th>
                        <th scope="col">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($elaborati as $elaborato)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $elaborato->titolo }}</td>
                            <td>{{ $elaborato->anno_scolastico }}</td>
                            <td>{{ $elaborato->collocazione }}</td>
                            <td>{{ $elaborato->file_mime_type }}</td>
                            <td>
                                {{ Illuminate\Support\Number::fileSize($elaborato->file_size) }}
                            </td>
                            <td>{{ strtolower($elaborato->note) }}</td>
                            <td>
                                <a
                                    href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                                    class="btn btn-primary btn-sm"
                                >
                                    Visualizza
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach ($elaborati as $elaborato)
                <div class="col">
                    <div class="card h-100" style="width: 18rem">
                        @if ($elaborato->getCoverImagePath())
                            <img
                                src="{{ $elaborato->getCoverImagePath() }}"
                                class="card-img-top"
                            />
                        @else
                            <img
                                src="{{ asset("images/placeholder.svg") }}"
                                class="card-img-top"
                            />
                        @endif
                        <div class="card-footer mt-auto">
                            <p class="card-text">{{ $elaborato->titolo }}</p>

                            <span class="badge rounded-pill bg-secondary">
                                {{ $elaborato->anno_scolastico }}
                            </span>

                            @if ($elaborato->collocazione)
                                <span class="badge rounded-pill bg-primary">
                                    {{ $elaborato->collocazione }}
                                </span>
                            @endif

                            @if ($elaborato->file_path)
                                <span
                                    class="badge rounded-pill bg-dark-subtle text-dark-emphasis"
                                >
                                    {{ Illuminate\Support\Number::fileSize($elaborato->file_size) }}
                                </span>
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
    @endif
@endsection
