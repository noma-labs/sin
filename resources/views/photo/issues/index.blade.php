@extends("photo.main")

@php
    $issueLabels = [
        "not_yet_born" => "Persona non ancora nata",
        "already_deceased" => "Persona già deceduta",
    ];
@endphp

@section("title", "Problemi Foto")

@section("content")
    @if ($issues->total() > 0)
        <div class="alert alert-warning mb-4">
            <strong>Totale problemi:</strong>
            {{ $issues->total() }} foto con problemi rilevati
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Problema {{ $issues->currentPage() }} di
                        {{ $issues->lastPage() }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                @foreach ($issues as $issue)
                    <div class="row">
                        <div class="col-md-6">
                            <img
                                src="{{ route("photos.preview", [$issue->photo_id, "draw_faces" => true, "highlight_face" => $issue->photo_persona_name]) }}"
                                alt="{{ $issue->file_name }}"
                                class="img-fluid"
                                style="max-height: 500px; object-fit: contain"
                            />
                        </div>

                        <div class="col-md-6">
                            <p
                                class="fw-semibold text-secondary mb-1 small text-uppercase"
                            >
                                Foto
                            </p>
                            <dl class="row mb-3">
                                <dt class="col-sm-5">Tipo Problema</dt>
                                <dd class="col-sm-7">
                                    <span class="badge text-bg-warning fs-6">
                                        {{ $issueLabels[$issue->issue_type] ?? $issue->issue_type }}
                                    </span>
                                </dd>
                                <dt class="col-sm-5">File</dt>
                                <dd class="col-sm-7">
                                    <a
                                        href="{{ route("photos.show", $issue->photo_id) }}"
                                        class="text-decoration-none"
                                    >
                                        {{ $issue->file_name }}
                                    </a>
                                </dd>
                                <dt class="col-sm-5">Percorso</dt>
                                <dd class="col-sm-7">
                                    <small class="text-muted">
                                        {{ $issue->source_file }}
                                    </small>
                                </dd>
                                <dt class="col-sm-5">Data Foto</dt>
                                <dd
                                    class="col-sm-7 d-flex align-items-center gap-2"
                                >
                                    {{ $issue->taken_at ? \Illuminate\Support\Carbon::parse($issue->taken_at)->format("Y-m-d") : "N/A" }}
                                    <x-modal
                                        modal-title="Modifica Data Foto"
                                        button-title="✏️ Modifica"
                                        button-style="btn-sm btn-outline-primary py-0"
                                    >
                                        <x-slot:body>
                                            <form
                                                method="POST"
                                                action="{{ route("photos.issues.update", $issue->id) }}"
                                                id="formAggiornaData-{{ $issue->id }}"
                                            >
                                                @csrf
                                                @method("PUT")

                                                <div class="mb-3">
                                                    <label
                                                        for="taken_at_{{ $issue->id }}"
                                                        class="form-label"
                                                    >
                                                        Data Foto
                                                    </label>
                                                    <input
                                                        type="text"
                                                        class="form-control @error("taken_at") is-invalid @enderror"
                                                        id="taken_at_{{ $issue->id }}"
                                                        name="taken_at"
                                                        value="{{ old("taken_at", $issue->taken_at ? \Illuminate\Support\Carbon::parse($issue->taken_at)->format("Y-m-d") : "") }}"
                                                        placeholder="yyyy-mm-dd"
                                                        pattern="\d{4}-\d{2}-\d{2}"
                                                    />
                                                    @error("taken_at")
                                                        <div
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </form>
                                        </x-slot>
                                        <x-slot:footer>
                                            <button
                                                class="btn btn-primary"
                                                type="submit"
                                                form="formAggiornaData-{{ $issue->id }}"
                                            >
                                                Salva
                                            </button>
                                        </x-slot>
                                    </x-modal>
                                </dd>

                                @if ($issue->location)
                                    <dt class="col-sm-5">Luogo</dt>
                                    <dd class="col-sm-7">
                                        {{ $issue->location }}
                                    </dd>
                                @endif

                                @if ($issue->description)
                                    <dt class="col-sm-5">Descrizione</dt>
                                    <dd class="col-sm-7">
                                        {{ $issue->description }}
                                    </dd>
                                @endif

                                @if ($issue->datnum)
                                    <dt class="col-sm-5">DANUM</dt>
                                    <dd class="col-sm-7">
                                        {{ $issue->datnum }}
                                    </dd>
                                @endif

                                @if ($issue->anum)
                                    <dt class="col-sm-5">ANUM</dt>
                                    <dd class="col-sm-7">
                                        {{ $issue->anum }}
                                    </dd>
                                @endif
                            </dl>

                            <hr class="my-2" />

                            <p
                                class="fw-semibold text-secondary mb-1 small text-uppercase"
                            >
                                Persona
                            </p>
                            <dl class="row mb-3">
                                <dt class="col-sm-5">Nome</dt>
                                <dd class="col-sm-7">
                                    <a
                                        href="{{ route("nomadelfia.person.show", $issue->persona_id) }}"
                                        class="text-decoration-none"
                                    >
                                        {{ $issue->photo_persona_name }}
                                        ({{ $issue->nome }}
                                        {{ $issue->cognome }})
                                    </a>
                                </dd>

                                <dt class="col-sm-5">Data Nascita</dt>
                                <dd class="col-sm-7">
                                    {{ $issue->data_nascita ? \Illuminate\Support\Carbon::parse($issue->data_nascita)->format("Y-m-d") : "N/A" }}
                                </dd>

                                @if ($issue->data_decesso)
                                    <dt class="col-sm-5">Data Decesso</dt>
                                    <dd class="col-sm-7">
                                        {{ \Illuminate\Support\Carbon::parse($issue->data_decesso)->format("Y-m-d") }}
                                    </dd>
                                @endif
                            </dl>

                            <x-modal
                                modal-title="Segna Come Risolto"
                                button-title="Segna Come Risolto"
                                button-style="btn-sm btn-success"
                            >
                                <x-slot:body>
                                    <form
                                        method="POST"
                                        action="{{ route("photos.issues.resolve", $issue->id) }}"
                                        id="form-resolve-{{ $issue->id }}"
                                    >
                                        @csrf
                                        <div class="mb-3">
                                            <label
                                                for="note_{{ $issue->id }}"
                                                class="form-label"
                                            >
                                                Nota (opzionale)
                                            </label>
                                            <textarea
                                                class="form-control"
                                                id="note_{{ $issue->id }}"
                                                name="note"
                                                rows="3"
                                                placeholder="Aggiungi una nota opzionale..."
                                            ></textarea>
                                        </div>
                                    </form>
                                </x-slot>
                                <x-slot:footer>
                                    <button
                                        class="btn btn-success"
                                        type="submit"
                                        form="form-resolve-{{ $issue->id }}"
                                    >
                                        Conferma
                                    </button>
                                </x-slot>
                            </x-modal>
                        </div>
                    </div>
                @endforeach
            </div>

            <div
                class="card-footer d-flex justify-content-between align-items-center"
            >
                @if ($issues->onFirstPage())
                    <button class="btn btn-secondary" disabled>
                        ← Indietro
                    </button>
                @else
                    <a
                        href="{{ $issues->previousPageUrl() }}"
                        class="btn btn-primary"
                    >
                        ← Indietro
                    </a>
                @endif

                @if ($issues->hasMorePages())
                    <a
                        href="{{ $issues->nextPageUrl() }}"
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
        <div class="alert alert-success">
            <strong>Nessun problema rilevato!</strong>
            Tutte le persone hanno data di nascita precedente alla foto e non
            risultano decedute prima della foto.
        </div>
    @endif
@endsection
