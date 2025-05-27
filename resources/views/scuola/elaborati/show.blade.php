@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Gestione elaborato"])

    <div class="row">
        <div class="col-md-6">
            @if (empty($elaborato->file_path))
                <p>Nessun file digitale</p>
                <form
                    method="POST"
                    action="{{ route("scuola.elaborati.media.store", ["id" => $elaborato->id]) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="mb-3">
                        <label for="file" class="form-label">Scegli file</label>
                        <input
                            type="file"
                            id="file"
                            name="file"
                            class="form-control"
                        />
                    </div>
                    <button class="btn btn-success" type="submit">
                        Carica
                    </button>
                </form>
            @else
                <div class="card">
                    <div class="card-body">
                        <iframe
                            src="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
                            width="100%"
                            height="600px"
                        ></iframe>
                        <a
                            href="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
                            class="stretched-link"
                        ></a>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Informazioni Elaborato</div>
                <div class="card-body">
                    <p><strong>Copertina:</strong></p>

                    @if ($elaborato->cover_image_path)
                        <img
                            src="{{ route("scuola.elaborati.cover.show", $elaborato->id) }}"
                            class="mb-3"
                            alt="Thumbnail"
                        />
                    @else
                        <a
                           src="{{ asset("images/placeholder.svg") }}"
                            class="btn btn-primary mb-3"
                        >
                            Carica copertina
                        </a>
                    @endif
                    <p>
                        <strong>Titolo:</strong>
                        {{ $elaborato->titolo }}
                    </p>
                    <p>
                        <strong>Descrizione:</strong>
                        {{ $elaborato->note }}
                    </p>
                    <p>
                        <strong>Anno Scolastico:</strong>
                        {{ $elaborato->anno_scolastico }}
                    </p>
                    <p>
                        <strong>Collocazione:</strong>
                        {{ $elaborato->collocazione }}
                    </p>
                    <p>
                        <strong>Dimensione:</strong>
                        {{ $elaborato->dimensione }}
                    </p>
                    @if ($elaborato->autore !== null && $elaborato->autore !== "")
                        <p class="alert alert-warning">
                            {{ $elaborato->autore }}
                        </p>
                    @endif
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                        class="btn btn-primary"
                    >
                        Download
                    </a>
                    <a
                        href="{{ route("scuola.elaborati.edit", $elaborato->id) }}"
                        class="btn btn-warning"
                    >
                        Modifica
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Studenti/Coordinatori</div>
                <div class="card-body">
                    <a
                        class="btn btn-primary mb-3"
                        href="{{ route("scuola.elaborati.students.create", $elaborato->id) }}"
                        role="button"
                    >
                        Importa studenti
                    </a>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Studenti:</strong>
                            <ul>
                                @forelse ($elaborato->studenti as $studente)
                                    <li>
                                        @include("scuola.templates.student", ["persona" => $studente])
                                    </li>
                                @empty
                                    <li>Nessuno studente.</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong>Coordinatori</strong>
                            <ul>
                                @forelse ($elaborato->coordinatori as $coordinatore)
                                    <li>
                                        @include("nomadelfia.templates.persona", ["persona" => $coordinatore])
                                    </li>
                                @empty
                                    <li>Nessuno $coordinatore.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
