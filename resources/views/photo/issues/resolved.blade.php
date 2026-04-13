@extends("photo.main")

@php
    $issueLabels = [
        "not_yet_born" => "Persona non ancora nata",
        "already_deceased" => "Persona già deceduta",
    ];
@endphp

@section("title", "Problemi Risolti")

@section("content")
    @if ($issues->total() > 0)
        <div class="alert alert-secondary mb-4">
            <strong>Totale problemi risolti:</strong>
            {{ $issues->total() }} problema/i risolti
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-secondary">
                    <tr>
                        <th>Foto</th>
                        <th>Tipo Problema</th>
                        <th>Persona</th>
                        <th>Data Attuale</th>
                        <th>Data Originale</th>
                        <th>Datnum</th>
                        <th>Anum</th>
                        <th>Descrizione</th>
                        <th>Note</th>
                        <th class="text-center">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($issues as $issue)
                        <tr>
                            <td style="width: 80px;">
                                <img
                                    src="{{ route("photos.preview", [$issue->photo_id, "draw_faces" => true, "highlight_face" => $issue->photo_persona_name]) }}"
                                    alt="{{ $issue->file_name }}"
                                    class="img-thumbnail"
                                    style="max-width: 80px; max-height: 80px; object-fit: contain;"
                                />
                            </td>
                            <td>
                                <span class="badge text-bg-warning">
                                    {{ $issueLabels[$issue->issue_type] ?? $issue->issue_type }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route("nomadelfia.person.show", $issue->persona_id) }}" class="text-decoration-none">
                                    {{ $issue->photo_persona_name }}
                                </a>
                                <br />
                                <small class="text-muted">{{ $issue->nome }} {{ $issue->cognome }}</small>
                            </td>
                            <td>
                                <strong>{{ $issue->taken_at ? \Illuminate\Support\Carbon::parse($issue->taken_at)->format("Y-m-d") : "N/A" }}</strong>
                            </td>
                            <td>
                                @if (count($issue->date_changes) > 0)
                                    <span class="text-decoration-line-through text-muted">
                                        {{ $issue->date_changes[0]['from'] }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $issue->datnum ?? '—' }}</small>
                            </td>
                            <td>
                                <small>{{ $issue->anum ?? '—' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $issue->description ?? '—' }}</small>
                            </td>
                            <td>
                                @if (count($issue->plain_notes) > 0)
                                    @foreach ($issue->plain_notes as $noteLine)
                                        <small class="d-block">{{ $noteLine }}</small>
                                    @endforeach
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form
                                    method="POST"
                                    action="{{ route("photos.issues.unresolve", $issue->id) }}"
                                    style="display: inline;"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-warning"
                                        title="Riapri Problema"
                                    >
                                        ↺
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($issues->hasPages())
            <nav aria-label="Navigazione pagine">
                <ul class="pagination justify-content-center">
                    @if ($issues->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">← Indietro</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $issues->previousPageUrl() }}">← Indietro</a>
                        </li>
                    @endif

                    @foreach ($issues->getUrlRange(1, $issues->lastPage()) as $page => $url)
                        @if ($page == $issues->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    @if ($issues->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $issues->nextPageUrl() }}">Avanti →</a>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ route("photos.issues.index") }}">Avanti → (Problemi Aperti)</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    @else
        <div class="alert alert-info">
            <strong>Nessun problema risolto!</strong>
            Non ci sono ancora problemi contrassegnati come risolti.
        </div>
    @endif
@endsection
