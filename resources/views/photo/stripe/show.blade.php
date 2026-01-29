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
            <div class="d-flex flex-wrap">
                @foreach ($stripe->photos as $photo)
                    <a
                        href="{{ route("photos.show", $photo->id) }}"
                        class="text-decoration-none"
                    >
                        <figure class="figure m-1" style="width: 18rem">
                            <div class="position-relative">
                                <img
                                    src="{{ route("photos.preview", $photo->id) }}"
                                    class="figure-img img-fluid rounded"
                                    alt="{{ $photo->description }}"
                                />
                                <div
                                    class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white"
                                >
                                    <div class="small">
                                        {{ $photo->file_name ?? "" }}
                                    </div>
                                    <div class="small">
                                        {{ $photo->taken_at ? $photo->taken_at->format("Y-m-d") : "N/A" }}
                                    </div>
                                    <div class="small">
                                        {{ $photo->subjects }}
                                    </div>
                                </div>
                            </div>
                        </figure>
                    </a>
                @endforeach
            </div>
        @else
            <div class="alert alert-info m-0">
                Nessuna foto collegata a questa striscia.
            </div>
        @endif
    </div>
@endsection
