@extends("photo.main")

@section("title", "Reconciliation")

@section("content")
    <div class="mt-1 mb-2 row g-2">
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div
                    class="card-body py-2 d-flex justify-content-between align-items-center"
                >
                    <p class="small text-muted mb-0">Unlinked Photos</p>
                    <p class="h5 mb-0 text-primary">
                        {{ $unlinkedPhotos->total() }}
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div
                    class="card-body py-2 d-flex justify-content-between align-items-center"
                >
                    <p class="small text-muted mb-0">Selected Photos</p>
                    <p class="h5 mb-0 text-success" id="selectedPhotosCount">
                        0
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light border-0">
                <div
                    class="card-body py-2 d-flex justify-content-between align-items-center"
                >
                    <p class="small text-muted mb-0">Selected DbfAll</p>
                    <p class="h5 mb-0 text-info" id="selectedDbfCount">0</p>
                </div>
            </div>
        </div>
    </div>

    <form
        method="POST"
        action="{{ route("photos.reconciliation.link") }}"
        id="reconciliationForm"
    >
        @csrf
        <input type="hidden" name="photoSearch" value="{{ $photoSearch }}" />
        <input type="hidden" name="dbfSearch" value="{{ $dbfSearch }}" />
        <div class="row g-3">
            <!-- Photos Column -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-bottom">
                        <div
                            class="d-flex justify-content-between align-items-center mb-3"
                        >
                            <h5 class="card-title mb-0">Unlinked Photos</h5>
                            <div class="d-flex gap-2">
                                <a
                                    href="{{ route("photos.reconciliation") }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    Reset
                                </a>
                                <button
                                    type="submit"
                                    id="linkButton"
                                    class="btn btn-sm btn-primary"
                                    disabled
                                >
                                    Link Selected (
                                    <span id="linkCount">0</span>
                                    )
                                </button>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <input
                                type="text"
                                id="photoSearchInput"
                                placeholder="Search by file_name..."
                                class="form-control form-control-sm"
                                value="{{ $photoSearch }}"
                            />
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="searchPhotos()"
                            >
                                Search
                            </button>
                        </div>
                    </div>

                    <div style="max-height: 400px; overflow-y: auto">
                        <div class="list-group list-group-flush">
                            @forelse ($unlinkedPhotos as $photo)
                                @php
                                    $modalId = "photo-modal-" . $photo->id;
                                @endphp

                                <label
                                    class="list-group-item list-group-item-action py-1 px-2"
                                >
                                    <div class="d-flex gap-2 align-items-start">
                                        <input
                                            type="checkbox"
                                            name="selectedPhotos[]"
                                            value="{{ $photo->id }}"
                                            class="form-check-input mt-1 photo-checkbox"
                                            onchange="updateCounts()"
                                        />
                                        <img
                                            src="{{ route("photos.preview", $photo->id) }}"
                                            alt="{{ $photo->file_name }}"
                                            style="
                                                width: 56px;
                                                height: 44px;
                                                object-fit: cover;
                                                flex-shrink: 0;
                                            "
                                            class="rounded border"
                                            loading="lazy"
                                        />
                                        <div
                                            class="flex-grow-1"
                                            style="min-width: 0"
                                        >
                                            <p
                                                class="mb-0 fw-medium text-truncate"
                                                style="font-size: 0.8rem"
                                            >
                                                {{ $photo->file_name }}
                                            </p>
                                            @if ($photo->source_file)
                                                <p
                                                    class="mb-0 text-muted text-truncate"
                                                    style="font-size: 0.7rem"
                                                >
                                                    {{ $photo->source_file }}
                                                </p>
                                            @endif

                                            @if ($photo->subjects)
                                                <p
                                                    class="mb-0 text-muted text-truncate"
                                                    style="font-size: 0.7rem"
                                                >
                                                    {{ $photo->subjects }}
                                                </p>
                                            @endif
                                        </div>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary ms-auto flex-shrink-0"
                                            data-bs-toggle="modal"
                                            data-bs-target="#{{ $modalId }}"
                                            onclick="event.preventDefault(); event.stopPropagation();"
                                        >
                                            <i>Open</i>
                                        </button>
                                    </div>
                                </label>
                            @empty
                                <div
                                    class="list-group-item text-center text-muted py-5"
                                >
                                    <p>No unlinked photos found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($unlinkedPhotos->hasPages())
                        <div class="card-footer bg-light">
                            {{ $unlinkedPhotos->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- DbfAll Column -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-3">Strisce</h5>
                        <div class="d-flex gap-2">
                            <input
                                type="text"
                                id="dbfSearchInput"
                                placeholder="Search by datnum, anum or descrizione..."
                                class="form-control form-control-sm"
                                value="{{ $dbfSearch }}"
                            />
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary"
                                onclick="searchDbf()"
                            >
                                Search
                            </button>
                        </div>
                    </div>

                    <div style="max-height: 400px; overflow-y: auto">
                        <div class="list-group list-group-flush">
                            @forelse ($dbfAllRecords as $dbf)
                                <label
                                    class="list-group-item list-group-item-action py-1 px-2"
                                >
                                    <div class="d-flex gap-2 align-items-start">
                                        <input
                                            type="radio"
                                            name="selectedDbfAll"
                                            value="{{ $dbf->id }}"
                                            class="form-check-input mt-1 dbf-checkbox"
                                            onchange="updateCounts()"
                                        />
                                        <div class="flex-grow-1">
                                            <p class="mb-1 fw-medium">
                                                Range:
                                                <code>
                                                    {{ $dbf->datnum }} -
                                                    {{ $dbf->anum }}
                                                </code>
                                                | Foto:
                                                <strong>
                                                    {{ $dbf->photos->count() }}
                                                </strong>
                                                | NFO:
                                                <strong>
                                                    {{ $dbf->nfo }}
                                                </strong>
                                                @php
                                                    $difference = $dbf->nfo - $dbf->photos->count();
                                                @endphp

                                                @if ($difference > 0)
                                                    | Mancanti:
                                                    <span
                                                        class="badge bg-warning"
                                                    >
                                                        {{ $difference }}
                                                    </span>
                                                @elseif ($difference === 0)
                                                    |
                                                    <span
                                                        class="badge bg-success"
                                                    >
                                                        Complete
                                                    </span>
                                                @else
                                                    | In più:
                                                    <span
                                                        class="badge bg-danger"
                                                    >
                                                        {{ abs($difference) }}
                                                    </span>
                                                @endif
                                            </p>
                                            <p class="small text-muted mb-1">
                                                Source:
                                                <code>{{ $dbf->source }}</code>
                                                @if ($dbf->data)
                                                    &nbsp;|&nbsp;
                                                    <strong>Data:</strong>
                                                    {{ $dbf->data->format("Y-m-d") }}
                                                @endif

                                                @if ($dbf->localita)
                                                    &nbsp;|&nbsp;
                                                    <strong>Località:</strong>
                                                    {{ $dbf->localita }}
                                                @endif

                                                @if ($dbf->argomento)
                                                    &nbsp;|&nbsp;
                                                    <strong>Argomento:</strong>
                                                    {{ $dbf->argomento }}
                                                @endif
                                            </p>
                                            @if ($dbf->descrizione)
                                                <p
                                                    class="small text-muted mb-1"
                                                >
                                                    {{ $dbf->descrizione }}
                                                </p>
                                            @endif

                                            <p class="small text-muted mb-0">
                                                ID: {{ $dbf->id }}
                                            </p>
                                            @if ($dbf->photos->isNotEmpty())
                                                <div
                                                    class="d-flex gap-2 flex-wrap mt-2"
                                                >
                                                    @foreach ($dbf->photos as $linkedPhoto)
                                                        <div
                                                            class="text-center"
                                                            style="width: 60px"
                                                        >
                                                            <img
                                                                src="{{ route("photos.preview", $linkedPhoto->id) }}"
                                                                alt="{{ $linkedPhoto->file_name }}"
                                                                style="
                                                                    width: 60px;
                                                                    height: 48px;
                                                                    object-fit: cover;
                                                                "
                                                                class="rounded border"
                                                                loading="lazy"
                                                            />
                                                            <p
                                                                class="mb-0 text-muted"
                                                                style="
                                                                    font-size: 0.6rem;
                                                                    word-break: break-all;
                                                                    line-height: 1.2;
                                                                "
                                                            >
                                                                {{ $linkedPhoto->file_name }}
                                                            </p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div
                                    class="list-group-item text-center text-muted py-5"
                                >
                                    <p>No DbfAll records found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    @if ($dbfAllRecords->hasPages())
                        <div class="card-footer bg-light">
                            {{ $dbfAllRecords->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Modals must live outside the form --}}
    @foreach ($unlinkedPhotos as $photo)
        @php
            $modalId = "photo-modal-" . $photo->id;
        @endphp

        <x-modal
            modal-title="Dettaglio foto"
            :modal-id="$modalId"
            fullscreen="true"
        >
            <x-slot:body>
                <div class="d-flex flex-column align-items-center">
                    <img
                        src="{{ route("photos.preview", $photo->id) }}"
                        class="img-fluid mb-2"
                        alt="{{ $photo->file_name }}"
                    />
                    <div><strong>{{ $photo->file_name ?? "" }}</strong></div>
                    <div>
                        {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                    </div>
                    <div>{{ $photo->subject ?? "" }}</div>
                    <div class="mt-2">{{ $photo->description }}</div>
                </div>
            </x-slot>
        </x-modal>
    @endforeach

    <script>
        function updateCounts() {
            const photoCheckboxes = document.querySelectorAll(
                '.photo-checkbox:checked',
            ).length;
            const dbfSelected =
                document.querySelector('.dbf-checkbox:checked') !== null;

            document.getElementById('selectedPhotosCount').textContent =
                photoCheckboxes;
            document.getElementById('selectedDbfCount').textContent =
                dbfSelected ? 1 : 0;
            document.getElementById('linkCount').textContent =
                photoCheckboxes + ' to ' + (dbfSelected ? 1 : 0);

            // Enable button only if both have selections
            document.getElementById('linkButton').disabled =
                photoCheckboxes === 0 || !dbfSelected;
        }

        function searchPhotos() {
            const searchValue =
                document.getElementById('photoSearchInput').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('photoSearch', searchValue);
            window.location.href = currentUrl.toString();
        }

        function searchDbf() {
            const searchValue = document.getElementById('dbfSearchInput').value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('dbfSearch', searchValue);
            currentUrl.searchParams.set('photoSearch', '{{ $photoSearch }}');
            window.location.href = currentUrl.toString();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateCounts);
    </script>
@endsection
