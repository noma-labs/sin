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
                <a
                    href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                    class="btn btn-primary"
                >
                    Scarica PDF
                </a>
                <iframe
                    src="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
                    width="100%"
                    height="100%"
                    title="{{ $elaborato->titolo }}"
                >
                    This browser does not support PDFs. Please download the PDF
                    to view it:
                    <a
                        href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                    >
                        Download PDF
                    </a>
                    .
                </iframe>
            @endif
        </div>

        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="collocazione" class="control-label">
                        Collocazione
                    </label>
                    <p class="form-control">{{ $elaborato->collocazione }}</p>
                </div>
                <div class="col-md-8">
                    <label for="titolo" class="control-label">Titolo</label>
                    <p class="form-control w-auto">{{ $elaborato->titolo }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="anno_scolastico" class="control-label">
                        Anno Scolastico
                    </label>
                    <p class="form-control">
                        {{ $elaborato->anno_scolastico }}
                    </p>
                </div>
                <div class="col-md-8">
                    <label for="classi" class="control-label">Classi</label>
                    <p class="form-control">{{ $elaborato->classi }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label for="anno_scolastico" class="control-label">
                        Rilegatura
                    </label>
                    <p class="form-control">
                        {{ $elaborato->rilegatura }}
                    </p>
                </div>
                <div class="col-md-8">
                    <label for="classi" class="control-label">Dimensioni</label>
                    <p class="form-control">{{ $elaborato->dimensione }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="note" class="control-label">Note</label>
                    <p class="form-control h-auto">{{ $elaborato->note }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <strong>Studenti:</strong>
                    <ul>
                        @forelse ($elaborato->studenti as $studente)
                            <li>
                                @include("nomadelfia.templates.persona", ["persona" => $studente])
                            </li>
                        @empty
                            <li>Nessuno studente.</li>
                        @endforelse
                    </ul>
                </div>

                <a
                href="{{ route("scuola.elaborati.students.edit', $elaborato->id) }}"
                class="btn btn-warning"
            >
                Modifica
            </a>
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
                        href="{{ route("scuola.elaborati.edit", $elaborato->id) }}"
                        class="btn btn-warning"
                    >
                        Modifica
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
