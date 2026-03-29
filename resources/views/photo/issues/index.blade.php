@extends("photo.main")

@section("title", "Problemi Foto")

@section("content")
    @if ($issues->total() > 0)
        <div class="alert alert-warning mb-4">
            <strong>Totale problemi:</strong> {{ $issues->total() }} foto con problemi rilevati
        </div>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Problema {{ $issues->currentPage() }} di {{ $issues->lastPage() }}
                    </div>
                    <div>
                        @foreach ($issues as $issue)
                            <span class="badge text-bg-light fs-6">{{ $issue->issue_type }}</span>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-body">
                @foreach ($issues as $issue)
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('photos.show', $issue->photo_id) }}" class="d-block">
                                <img
                                    src="{{ route('photos.preview', [$issue->photo_id, 'draw_faces' => true]) }}"
                                    alt="{{ $issue->file_name }}"
                                    class="img-fluid"
                                    style="max-height: 500px; object-fit: contain;"
                                />
                            </a>
                        </div>

                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">File</dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('photos.show', $issue->photo_id) }}" class="text-decoration-none">
                                        {{ $issue->file_name }}
                                    </a>
                                </dd>

                                <dt class="col-sm-5">Tipo Problema</dt>
                                <dd class="col-sm-7">
                                    <span class="badge text-bg-warning fs-6">{{ $issue->issue_type }}</span>
                                </dd>

                                <dt class="col-sm-5">Persona</dt>
                                <dd class="col-sm-7">
                                    @if ($issue->persona_id)
                                        <a href="{{ route('photos.index', ['name' => $issue->cognome]) }}" class="text-decoration-none">
                                            {{ Illuminate\Support\Str::title($issue->nome) }}
                                            {{ Illuminate\Support\Str::title($issue->cognome) }}
                                        </a>
                                    @else
                                        <span class="text-muted">Non assegnato</span>
                                    @endif
                                </dd>

                                <dt class="col-sm-5">Data Foto</dt>
                                <dd class="col-sm-7">{{ $issue->taken_at ? \Illuminate\Support\Carbon::parse($issue->taken_at)->format('d/m/Y') : 'N/A' }}</dd>

                                <dt class="col-sm-5">Data Nascita</dt>
                                <dd class="col-sm-7">{{ $issue->data_nascita ? \Illuminate\Support\Carbon::parse($issue->data_nascita)->format('d/m/Y') : 'N/A' }}</dd>

                                @if ($issue->data_decesso)
                                    <dt class="col-sm-5">Data Decesso</dt>
                                    <dd class="col-sm-7">{{ \Illuminate\Support\Carbon::parse($issue->data_decesso)->format('d/m/Y') }}</dd>
                                @endif

                                @if ($issue->days_diff !== null)
                                    <dt class="col-sm-5">Giorni Differenza</dt>
                                    <dd class="col-sm-7">
                                        <span class="badge text-bg-danger">{{ abs($issue->days_diff) }} giorni</span>
                                    </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                @if ($issues->onFirstPage())
                    <button class="btn btn-secondary" disabled>← Indietro</button>
                @else
                    <a href="{{ $issues->previousPageUrl() }}" class="btn btn-primary">← Indietro</a>
                @endif

                <span class="text-muted">
                    Pagina {{ $issues->currentPage() }} di {{ $issues->lastPage() }}
                </span>

                @if ($issues->hasMorePages())
                    <a href="{{ $issues->nextPageUrl() }}" class="btn btn-primary">Avanti →</a>
                @else
                    <button class="btn btn-secondary" disabled>Avanti →</button>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-success">
            <strong>Nessun problema rilevato!</strong> Tutte le persone hanno data di nascita precedente alla foto e non risultano decedute prima della foto.
        </div>
    @endif
@endsection
