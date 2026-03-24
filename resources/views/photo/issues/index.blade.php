@extends("photo.main")

@section("title", "Problemi Foto")

@section("content")
    <div class="container-fluid m-3">
        <h2 class="mb-4">Problemi Foto</h2>
        <p class="text-muted">Foto dove la persona non era ancora nata o già deceduta</p>

        @if ($issues->count() > 0)
            <div class="d-flex flex-column gap-3">
                @foreach ($issues as $issue)
                    <div class="card border-danger">
                        <div class="card-body d-flex gap-3 align-items-start">
                            <a href="{{ route('photos.show', $issue->photo_id) }}" class="flex-shrink-0">
                                <img
                                    src="{{ route('photos.preview', [$issue->photo_id, 'draw_faces' => true]) }}"
                                    alt="{{ $issue->file_name }}"
                                    style="max-height: 200px; max-width: 260px; object-fit: contain;"
                                />
                            </a>
                            <div class="flex-grow-1">
                                <div class="mb-2">
                                    <span class="badge text-bg-warning fs-6">{{ $issue->issue_type }}</span>
                                </div>
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">File</dt>
                                    <dd class="col-sm-8">
                                        <a href="{{ route('photos.show', $issue->photo_id) }}" class="text-decoration-none">
                                            {{ $issue->file_name }}
                                        </a>
                                    </dd>

                                    <dt class="col-sm-4">Data Foto</dt>
                                    <dd class="col-sm-8">{{ $issue->taken_at ? \Illuminate\Support\Carbon::parse($issue->taken_at)->format('d/m/Y') : 'N/A' }}</dd>

                                    <dt class="col-sm-4">Persona</dt>
                                    <dd class="col-sm-8">
                                        @if ($issue->persona_id)
                                            <a href="{{ route('photos.index', ['name' => $issue->cognome]) }}" class="text-decoration-none">
                                                {{ Illuminate\Support\Str::title($issue->nome) }}
                                                {{ Illuminate\Support\Str::title($issue->cognome) }}
                                            </a>
                                        @else
                                            <span class="text-muted">Non assegnato</span>
                                        @endif
                                    </dd>

                                    <dt class="col-sm-4">Data Nascita</dt>
                                    <dd class="col-sm-8">{{ $issue->data_nascita ? \Illuminate\Support\Carbon::parse($issue->data_nascita)->format('d/m/Y') : 'N/A' }}</dd>

                                    @if ($issue->days_diff !== null)
                                        <dt class="col-sm-4">Giorni Differenza</dt>
                                        <dd class="col-sm-8">
                                            <span class="badge text-bg-danger">{{ $issue->days_diff }} giorni</span>
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $issues->links("vendor.pagination.bootstrap-5") }}
            </div>
        @else
            <div class="alert alert-success">
                <strong>Nessun problema rilevato!</strong> Tutte le persone hanno data di nascita precedente alla foto e non risultano decedute prima della foto.
            </div>
        @endif
    </div>
@endsection
