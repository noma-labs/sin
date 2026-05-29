@extends("archive.layout")

@section("content")
    <div class="mb-4">
        <h1 class="h3 mb-3">Risoluzione Problemi</h1>

        @if (session("status"))
            <div class="alert alert-success" role="alert">
                {{ session("status") }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif
    </div>

    @if ($orphanedTranscripts->total() > 0)
        <div class="row mb-3">
            <div class="col">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-body py-2 px-3 d-flex align-items-center gap-2"
                    >
                        <span class="text-muted small">Problemi</span>
                        <span class="fw-bold fs-5">
                            {{ $orphanedTranscripts->currentPage() }} di
                            {{ $orphanedTranscripts->lastPage() }}
                        </span>
                        discorsi senza transcrizioni associate.
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @foreach ($orphanedTranscripts as $transcript)
                    <div class="row">
                        <div class="col-lg-4 mb-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    @if ($transcript->heading)
                                        <h6
                                            class="card-subtitle mb-3 text-muted"
                                        >
                                            {{ $transcript->heading }}
                                        </h6>
                                    @endif

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-2">
                                            <strong>File:</strong>
                                            <br />
                                            <code class="text-break">
                                                {{ $transcript->file_path }}
                                            </code>
                                        </small>
                                    </div>

                                    <hr />

                                    <div class="preview-content">
                                        <h6 class="small mb-2">
                                            Anteprima Contenuto:
                                        </h6>
                                        <p
                                            class="small text-justify"
                                            style="
                                                max-height: 300px;
                                                overflow-y: auto;
                                            "
                                        >
                                            {{ implode(" ", array_slice(explode(" ", $transcript->content ?? ""), 0, 250)) }}
                                            @if (str_word_count($transcript->content ?? "") > 250)
                                                ...
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card border-0">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        Seleziona trascrizione
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if ($matchingRecordings->count() > 0)
                                        <form
                                            method="POST"
                                            action="{{ route("archive.troubleshooting.assign") }}"
                                            class="mb-3"
                                        >
                                            @csrf
                                            <input
                                                type="hidden"
                                                name="transcript_id"
                                                value="{{ $transcript->id }}"
                                            />
                                            <input
                                                type="hidden"
                                                name="page"
                                                value="{{ $orphanedTranscripts->currentPage() }}"
                                            />
                                            <p class="text-muted mb-3">
                                                Recording trovati:
                                                <strong>
                                                    {{ $matchingRecordings->count() }}
                                                </strong>
                                            </p>

                                            <div
                                                class="list-group"
                                                style="
                                                    max-height: 520px;
                                                    overflow-y: auto;
                                                "
                                            >
                                                @foreach ($matchingRecordings as $recording)
                                                    <label
                                                        class="list-group-item d-flex gap-3 p-3 cursor-pointer"
                                                        style="cursor: pointer"
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="recording_id"
                                                            value="{{ $recording->id }}"
                                                            class="form-check-input mt-1"
                                                            style="
                                                                cursor: pointer;
                                                            "
                                                            required
                                                        />
                                                        <div
                                                            class="flex-grow-1"
                                                        >
                                                            <div class="mb-1">
                                                                <strong>
                                                                    {{ $recording->code ?? "N/A" }}
                                                                </strong>
                                                                @if ($recording->GENERE)
                                                                    <span
                                                                        class="badge text-bg-info ms-2"
                                                                    >
                                                                        {{ $recording->GENERE }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <small
                                                                class="text-muted d-block"
                                                            >
                                                                <strong>
                                                                    Data:
                                                                </strong>
                                                                {{ $recording->DATA?->format("d/m/Y") ?? "N/A" }}
                                                                @if ($recording->ORE)
                                                                    · Ore:
                                                                    {{ $recording->ORE }}
                                                                @endif
                                                            </small>
                                                            @if ($recording->AUTORE)
                                                                <small
                                                                    class="text-muted d-block"
                                                                >
                                                                    <strong>
                                                                        Autore:
                                                                    </strong>
                                                                    {{ $recording->AUTORE }}
                                                                </small>
                                                            @endif

                                                            @if ($recording->DESTINATARI)
                                                                <small
                                                                    class="text-muted d-block"
                                                                >
                                                                    <strong>
                                                                        Destinatari:
                                                                    </strong>
                                                                    {{ $recording->DESTINATARI }}
                                                                </small>
                                                            @endif

                                                            @php($localita = $recording->getAttribute('LOCALITA'))
                                                            @if ($localita)
                                                                <small
                                                                    class="text-muted d-block"
                                                                >
                                                                    <strong>
                                                                        Luogo:
                                                                    </strong>
                                                                    {{ $localita }}
                                                                </small>
                                                            @endif

                                                            @if ($recording->ARGOMENTO)
                                                                <small
                                                                    class="text-muted d-block"
                                                                >
                                                                    <strong>
                                                                        Argomento:
                                                                    </strong>
                                                                    {{ $recording->ARGOMENTO }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>

                                            <div class="mt-3">
                                                <button
                                                    type="submit"
                                                    class="btn btn-primary btn-sm"
                                                    id="assignBtn"
                                                    disabled
                                                >
                                                    Assegna trascrizione
                                                </button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            Nessun recording trovato per il
                                            periodo
                                            {{ substr($transcript->code, 0, 6) }}.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <script>
                            document
                                .querySelectorAll('input[name="recording_id"]')
                                .forEach((radio) => {
                                    radio.addEventListener(
                                        'change',
                                        function () {
                                            document.getElementById(
                                                'assignBtn',
                                            ).disabled = !this.checked;
                                        },
                                    );
                                });
                        </script>
                    </div>
                @endforeach
            </div>

            <div
                class="card-footer d-flex justify-content-between align-items-center"
            >
                @if ($orphanedTranscripts->onFirstPage())
                    <button class="btn btn-secondary" disabled>
                        ← Indietro
                    </button>
                @else
                    <a
                        href="{{ $orphanedTranscripts->previousPageUrl() }}"
                        class="btn btn-primary"
                    >
                        ← Indietro
                    </a>
                @endif

                @if ($orphanedTranscripts->hasMorePages())
                    <a
                        href="{{ $orphanedTranscripts->nextPageUrl() }}"
                        class="btn btn-primary"
                    >
                        Avanti →
                    </a>
                @else
                    <button class="btn btn-secondary" disabled>Avanti →</button>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-success" role="alert">
            <strong>Nessun problema trovato!</strong>
            Tutti i transcript sono correttamente associati a un recording.
        </div>
    @endif
@endsection
