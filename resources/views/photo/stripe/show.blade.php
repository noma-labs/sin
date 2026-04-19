@extends("photo.main")

@section("content")
    <div class="my-3">
        <div class="card mb-3">
            <div class="card-header">Dettagli striscia</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Striscia</span>
                    <span class="fw-semibold">
                        {{ $stripe->datnum }}
                        @if (! empty($stripe->anum) && $stripe->anum !== $stripe->datnum)
                                - {{ $stripe->anum }}
                        @endif
                    </span>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <span class="text-muted">NFO</span>
                    <span class="fw-semibold">
                        {{ ! is_null($stripe->nfo) ? $stripe->nfo : "N/A" }}
                        @if (! is_null($stripe->nfo))
                            @if ($stripe->nfo > $photoCount)
                                <span class="badge text-bg-warning ms-2">
                                    {{ $stripe->nfo - $photoCount }} foto
                                    mancanti
                                </span>
                            @elseif ($photoCount > $stripe->nfo)
                                <span class="badge text-bg-danger ms-2">
                                    {{ $photoCount - $stripe->nfo }} foto in
                                    più
                                </span>
                            @endif
                        @endif
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Data</span>
                    <span class="fw-semibold">
                        {{ ! empty($stripe->data) ? $stripe->data : "N/A" }}
                    </span>
                </li>

                <li class="list-group-item">
                    <div class="d-flex justify-content-between flex-wrap">
                        <span>
                            <span class="text-muted">
                                SC (scannerizzazione)
                            </span>
                            <span class="fw-semibold ms-1">
                                {{ ! empty($stripe->sc) ? $stripe->sc : "N/A" }}
                            </span>
                        </span>
                        <span>
                            <span class="text-muted">FI (file partenza)</span>
                            <span class="fw-semibold ms-1">
                                {{ ! empty($stripe->fi) ? $stripe->fi : "N/A" }}
                            </span>
                        </span>
                        <span>
                            <span class="text-muted">
                                TP (tipo di pellicola)
                            </span>
                            <span class="fw-semibold ms-1">
                                {{ ! empty($stripe->tp) ? $stripe->tp : "N/A" }}
                            </span>
                        </span>
                    </div>
                </li>

                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Località</span>
                    <span class="fw-semibold">
                        {{ ! empty($stripe->localita) ? $stripe->localita : "N/A" }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Argomento</span>
                    <span class="fw-semibold">
                        {{ ! empty($stripe->argomento) ? $stripe->argomento : "N/A" }}
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Descrizione</span>
                    <span class="fw-semibold">
                        {{ ! empty($stripe->descrizione) ? $stripe->descrizione : "N/A" }}
                    </span>
                </li>

                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <span class="text-muted">Sorgente</span>
                    <span class="badge text-bg-secondary">
                        {{ $stripe->source }}
                    </span>
                </li>
            </ul>
        </div>

        @if ($photoCount)
            <div class="d-flex flex-wrap gap-3">
                @foreach ($stripe->photos as $photo)
                    <div class="card" style="width: 18rem">
                        <a href="{{ route("photos.show", $photo->id) }}" class="text-decoration-none text-reset">
                            <div class="position-relative">
                                <img
                                    src="{{ route("photos.preview", $photo->id) }}"
                                    class="card-img-top rounded-top"
                                    alt="{{ $photo->description }}"
                                    style="height: 200px; object-fit: cover"
                                />
                            </div>
                        </a>
                        <div class="card-body py-2 px-2">
                            <div class="small fw-semibold text-truncate">{{ $photo->file_name }}</div>
                            <div class="small text-muted">{{ $photo->taken_at ? $photo->taken_at->format("Y-m-d") : "N/A" }}</div>
                        </div>
                        @if ($photo->persone->isNotEmpty())
                            <div class="card-body p-0 border-top">
                                <div class="p-2 small">
                                    @foreach ($photo->persone as $person)
                                        <span>{{ $person->pivot->persona_nome }},</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info m-0">
                Nessuna foto collegata a questa striscia.
            </div>
        @endif
    </div>
@endsection
