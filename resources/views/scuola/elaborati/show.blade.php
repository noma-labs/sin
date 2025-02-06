@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Gestione elaborato"])

    <div class="row">
        <div class="col-md-8">
            @if (empty($elaborato->file_path))
                <p>Nessun file digitale</p>
                <form
                    method="POST"
                    action="{{ route("scuola.elaborati.media.store", ["id" => $elaborato->id]) }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="file" class="form-label">
                                Scegli file
                            </label>
                            <input type="file" id="file" name="file" />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button class="btn btn-success" type="submit">
                                Carica
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="mb-3">
                    <a
                        href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                        class="btn btn-primary"
                    >
                        Scarica PDF
                    </a>
                    <a
                        href="{{ route("scuola.elaborati.thumbnail.create", $elaborato->id) }}"
                        class="btn btn-primary"
                    >
                        Carica copertina
                    </a>
                </div>

                <img src="{{$elaborato->getCoverImagePath()}}" alt="Thumbnail" />

            @endif
        </div>

        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="collocazione" class="form-label">
                        Collocazione
                    </label>
                    <p class="form-control">{{ $elaborato->collocazione }}</p>
                </div>
                <div class="col-md-8">
                    <label for="titolo" class="form-label">Titolo</label>
                    <p class="form-control w-auto">{{ $elaborato->titolo }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="anno_scolastico" class="form-label">
                        Anno Scolastico
                    </label>
                    <p class="form-control">
                        {{ $elaborato->anno_scolastico }}
                    </p>
                </div>
                <div class="col-md-8">
                    <label for="classi" class="form-label">Classi</label>
                    <p class="form-control">{{ $elaborato->classi }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="anno_scolastico" class="form-label">
                        Rilegatura
                    </label>
                    <p class="form-control">
                        {{ $elaborato->rilegatura }}
                    </p>
                </div>
                <div class="col-md-8">
                    <label for="classi" class="form-label">Dimensioni</label>
                    <p class="form-control">{{ $elaborato->dimensione }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="note" class="form-label">Note</label>
                    <p class="form-control h-auto">{{ $elaborato->note }}</p>
                </div>
            </div>
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
            <div class="row">
                <div class="col-md-12">
                    <a
                        class="btn btn-primary"
                        href="{{ route("scuola.elaborati.students.create", $elaborato->id) }}"
                        role="button"
                    >
                        Importa studenti
                    </a>
                    <div class="d-flex justify-content-end">
                        <a
                            href="{{ route("scuola.elaborati.edit", $elaborato->id) }}"
                            class="btn btn-warning"
                        >
                            Modifica
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
