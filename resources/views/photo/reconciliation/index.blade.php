@extends("photo.main")

@section("title", "Photo-DbfAll Reconciliation")

@section("content")
 @include("partials.header", ["title" => "Risoluzione Foto senza Strisce", "subtitle" => "Collega le foto non collegate alle corrispondenti strisce"])

<div class="mb-3 row g-3">
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <p class="small text-muted mb-2">Unlinked Photos</p>
                <p class="h3 mb-0 text-primary">{{ $unlinkedPhotos->total() }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <p class="small text-muted mb-2">Selected Photos</p>
                <p class="h3 mb-0 text-success" id="selectedPhotosCount">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light border-0">
            <div class="card-body">
                <p class="small text-muted mb-2">Selected DbfAll</p>
                <p class="h3 mb-0 text-info" id="selectedDbfCount">0</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Form -->
<form method="POST" action="{{ route('photos.reconciliation.link') }}" id="reconciliationForm">
    @csrf
    <div class="row g-3">
        <!-- Photos Column -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-3">Unlinked Photos</h5>
                    <div class="d-flex gap-2">
                        <input
                            type="text"
                            id="photoSearchInput"
                            placeholder="Search by file_name..."
                            class="form-control form-control-sm"
                            value="{{ $photoSearch }}"
                        />
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="searchPhotos()">Search</button>
                    </div>
                </div>

                <div style="max-height: 400px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($unlinkedPhotos as $photo)
                            <label class="list-group-item list-group-item-action">
                                <div class="d-flex gap-3 align-items-start">
                                    <input
                                        type="checkbox"
                                        name="selectedPhotos[]"
                                        value="{{ $photo->id }}"
                                        class="form-check-input mt-1 photo-checkbox"
                                        onchange="updateCounts()"
                                    />
                                    <img
                                        src="{{ route('photos.preview', $photo->id) }}"
                                        alt="{{ $photo->file_name }}"
                                        style="width: 80px; height: 60px; object-fit: cover; flex-shrink: 0;"
                                        class="rounded border"
                                        loading="lazy"
                                    />
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-medium text-truncate">{{ $photo->file_name }}</p>
                                        <p class="small text-muted mb-0">ID: {{ $photo->id }}</p>
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="list-group-item text-center text-muted py-5">
                                <p>No unlinked photos found</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($unlinkedPhotos->hasPages())
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
                    <h5 class="card-title mb-3">DbfAll Records</h5>
                    <div class="d-flex gap-2">
                        <input
                            type="text"
                            id="dbfSearchInput"
                            placeholder="Search by datnum, anum or descrizione..."
                            class="form-control form-control-sm"
                            value="{{ $dbfSearch }}"
                        />
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="searchDbf()">Search</button>
                    </div>
                </div>

                <div style="max-height: 400px; overflow-y: auto;">
                    <div class="list-group list-group-flush">
                        @forelse($dbfAllRecords as $dbf)
                            <label class="list-group-item list-group-item-action">
                                <div class="d-flex gap-3 align-items-start">
                                    <input
                                        type="radio"
                                        name="selectedDbfAll"
                                        value="{{ $dbf->id }}"
                                        class="form-check-input mt-1 dbf-checkbox"
                                        onchange="updateCounts()"
                                    />
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-medium">Range: <code>{{ $dbf->datnum }} - {{ $dbf->anum }}</code></p>
                                        <p class="small text-muted mb-1">
                                            Source: <code>{{ $dbf->source }}</code>
                                            @if($dbf->data)
                                                &nbsp;|&nbsp; <strong>Data:</strong> {{ $dbf->data->format('Y-m-d') }}
                                            @endif
                                            @if($dbf->localita)
                                                &nbsp;|&nbsp; <strong>Località:</strong> {{ $dbf->localita }}
                                            @endif
                                            @if($dbf->argomento)
                                                &nbsp;|&nbsp; <strong>Argomento:</strong> {{ $dbf->argomento }}
                                            @endif
                                        </p>
                                        @if($dbf->descrizione)
                                            <p class="small text-muted mb-1">{{ $dbf->descrizione }}</p>
                                        @endif
                                        <p class="small text-muted mb-0">ID: {{ $dbf->id }}</p>
                                        @if($dbf->photos->isNotEmpty())
                                            <div class="d-flex gap-1 flex-wrap mt-2">
                                                @foreach($dbf->photos as $linkedPhoto)
                                                    <img
                                                        src="{{ route('photos.preview', $linkedPhoto->id) }}"
                                                        alt="{{ $linkedPhoto->file_name }}"
                                                        style="width: 60px; height: 48px; object-fit: cover;"
                                                        class="rounded border"
                                                        loading="lazy"
                                                        title="{{ $linkedPhoto->file_name }}"
                                                    />
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="list-group-item text-center text-muted py-5">
                                <p>No DbfAll records found</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4 d-flex gap-2 justify-content-end">
        <a href="{{ route('photos.reconciliation') }}" class="btn btn-outline-secondary">Reset</a>
        <button type="submit" id="linkButton" class="btn btn-primary" disabled>
            Link Selected (<span id="linkCount">0</span>)
        </button>
    </div>
</form>

<script>
function updateCounts() {
    const photoCheckboxes = document.querySelectorAll('.photo-checkbox:checked').length;
    const dbfSelected = document.querySelector('.dbf-checkbox:checked') !== null;

    document.getElementById('selectedPhotosCount').textContent = photoCheckboxes;
    document.getElementById('selectedDbfCount').textContent = dbfSelected ? 1 : 0;
    document.getElementById('linkCount').textContent = photoCheckboxes + ' to ' + (dbfSelected ? 1 : 0);

    // Enable button only if both have selections
    document.getElementById('linkButton').disabled = photoCheckboxes === 0 || !dbfSelected;
}

function searchPhotos() {
    const searchValue = document.getElementById('photoSearchInput').value;
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