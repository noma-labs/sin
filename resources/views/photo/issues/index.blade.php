@extends("photo.main")

@php
    $issueLabels = [
        "not_yet_born" => "Persona non ancora nata",
        "already_deceased" => "Persona già deceduta",
    ];
@endphp

@section("title", "Problemi Foto")

@section("content")
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a
                class="nav-link {{ $status === "open" ? "active" : "" }}"
                href="{{ route("photos.issues.index", ["status" => "open"]) }}"
            >
                Problemi Aperti
                @if ($status === "open")
                    <span class="badge text-bg-danger ms-1">
                        {{ $issues->total() }}
                    </span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a
                class="nav-link {{ $status === "resolved" ? "active" : "" }}"
                href="{{ route("photos.issues.index", ["status" => "resolved"]) }}"
            >
                Problemi Risolti
                @if ($status === "resolved")
                    <span class="badge text-bg-secondary ms-1">
                        {{ $issues->total() }}
                    </span>
                @endif
            </a>
        </li>
    </ul>

    @if ($issues->total() > 0)
        @php
            $cardClass =
                $status === "open" ? "card border-danger" : "card border-secondary";
            $headerClass =
                $status === "open"
                    ? "card-header bg-danger text-white"
                    : "card-header bg-secondary text-white";
        @endphp

        <div class="{{ $cardClass }}">
            <div class="{{ $headerClass }}">
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
                            <dl class="row mb-2">
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
                                    @if ($status === "open")
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
                                    @endif
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
                            </dl>

                            @if ($issue->datnum || $issue->anum)
                                <p
                                    class="fw-semibold text-secondary mb-1 small text-uppercase"
                                >
                                    Striscia
                                </p>
                                <dl class="row mb-2">
                                    @if ($issue->datnum)
                                        <dt class="col-sm-5">DANUM</dt>
                                        <dd class="col-sm-7">
                                            <a
                                                href="{{ route("photos.stripes.show", $issue->dbf_id) }}"
                                                class="text-decoration-none"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                            >
                                                {{ $issue->datnum }} ↗
                                            </a>
                                        </dd>
                                    @endif

                                    @if ($issue->anum)
                                        <dt class="col-sm-5">ANUM</dt>
                                        <dd class="col-sm-7">
                                            {{ $issue->anum }}
                                        </dd>
                                    @endif
                                </dl>
                            @endif

                            @if ($status === "resolved")
                                <p
                                    class="fw-semibold text-secondary mb-1 small text-uppercase"
                                >
                                    Correzione
                                </p>
                                <dl class="row mb-3">
                                    @foreach ($issue->date_changes as $i => $change)
                                        <dt class="col-sm-5">
                                            Data Originale
                                            @if (count($issue->date_changes) > 1)
                                                <small class="text-muted">
                                                    #{{ $i + 1 }}
                                                </small>
                                            @endif
                                        </dt>
                                        <dd class="col-sm-7">
                                            <span
                                                class="text-decoration-line-through text-muted"
                                            >
                                                {{ $change["from"] }}
                                            </span>
                                        </dd>
                                        <dt class="col-sm-5">
                                            Data Corretta
                                            @if (count($issue->date_changes) > 1)
                                                <small class="text-muted">
                                                    #{{ $i + 1 }}
                                                </small>
                                            @endif
                                        </dt>
                                        <dd class="col-sm-7">
                                            <strong class="text-success">
                                                {{ $change["to"] }}
                                            </strong>
                                        </dd>
                                    @endforeach

                                    @if (count($issue->plain_notes) > 0)
                                        <dt class="col-sm-5">Note</dt>
                                        <dd class="col-sm-7">
                                            @foreach ($issue->plain_notes as $noteLine)
                                                <small class="d-block">
                                                    {{ $noteLine }}
                                                </small>
                                            @endforeach
                                        </dd>
                                    @endif

                                    <dt class="col-sm-5">Risolto il</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge text-bg-success">
                                            {{ \Illuminate\Support\Carbon::parse($issue->resolved_at)->format("d/m/Y H:i") }}
                                        </span>
                                    </dd>
                                </dl>
                            @endif

                            <hr />

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

                            @if ($status === "open")
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
                            @else
                                <form
                                    method="POST"
                                    action="{{ route("photos.issues.unresolve", $issue->id) }}"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        ↺ Apri Nuovamente
                                    </button>
                                </form>
                            @endif
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
                        href="{{ $issues->previousPageUrl() . "&status=" . $status }}"
                        class="btn btn-primary"
                    >
                        ← Indietro
                    </a>
                @endif

                @if ($issues->hasMorePages())
                    <a
                        href="{{ $issues->nextPageUrl() . "&status=" . $status }}"
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
        @if ($status === "open")
            <div class="alert alert-success">
                <strong>Nessun problema rilevato!</strong>
                Tutte le persone hanno data di nascita precedente alla foto e
                non risultano decedute prima della foto.
            </div>
        @else
            <div class="alert alert-info">
                <strong>Nessun problema risolto!</strong>
                Non ci sono ancora problemi contrassegnati come risolti.
            </div>
        @endif
    @endif
@endsection
