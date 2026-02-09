@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2 my-3">
        @foreach ($years as $year)
            <a
                href="{{ route("photos.folders.index", ["year" => $year->year, "name" => request("name")]) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @endforeach
    </div>

    @php($currentView = "grid")

    @php($children = isset($dirTree["children"]) ? $dirTree["children"] : [])
    <div class="row row-cols-2 row-cols-md-5 g-3">
        @php($sorted = collect($children)->sortBy(function ($child) {return is_string($child["label"] ?? null) ? $child["label"] : "";})->all())
        @php($seen = [])
        @foreach ($sorted as $label => $child)
            @php($folderLabel = is_string($child["label"] ?? null) ? $child["label"] : (is_string($label) ? $label : ""))
            @php($dedupeKey = $folderLabel)
            @if ($folderLabel === "")
                @continue
            @endif

            @if (isset($seen[$dedupeKey]))
                @continue
            @endif

            @php($seen[$dedupeKey] = true)
            @php($firstPhoto = $child["preview"] ?? (isset($child["photos"]) && count($child["photos"]) ? $child["photos"][0] : null))
            <div class="col">
                <a
                    href="{{ route("photos.folders.show", ["path" => $folderLabel]) }}"
                    class="text-decoration-none"
                >
                    <div class="card h-100 shadow-sm">
                        @if ($firstPhoto)
                            <img
                                src="{{ route("photos.preview", $firstPhoto->id) }}"
                                class="card-img-top"
                                alt="Anteprima cartella"
                            />
                        @else
                            <div
                                class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                style="height: 140px"
                            >
                                <span class="text-muted">
                                    Nessuna anteprima
                                </span>
                            </div>
                        @endif
                        <div class="card-body">
                            <div
                                class="d-flex justify-content-between align-items-center"
                            >
                                <h6 class="card-title mb-0">
                                    {{ $folderLabel }}
                                </h6>
                                @if (isset($child["total"]))
                                    <span class="badge text-bg-secondary">
                                        {{ (int) $child["total"] }}
                                    </span>
                                @endif
                            </div>
                            @if ($firstPhoto)
                                <div class="small text-muted mt-2">
                                    {{ $firstPhoto->file_name }}
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    @if ($photos->count() > 0)
        @if ($currentView === "grid")
            <div class="d-flex flex-wrap mt-4">
                @foreach ($photos as $photo)
                    <a href="{{ route("photos.show", $photo->id) }}">
                        <figure class="figure m-1" style="width: 25rem">
                            <div class="position-relative">
                                <img
                                    src="{{ route("photos.preview", $photo->id) }}"
                                    class="figure-img img-fluid rounded"
                                    alt="{{ $photo->description }}"
                                />
                                <div
                                    class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white"
                                >
                                    <span class="small">
                                        {{ $photo->file_name ?? "" }}
                                    </span>
                                    <span class="small">
                                        {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                    </span>
                                </div>
                            </div>
                        </figure>
                    </a>
                @endforeach
            </div>
        @else
            <div class="table-responsive mt-4">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Anteprima</th>
                            <th>Nome File</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($photos as $photo)
                            <tr>
                                <td style="width: 72px">
                                    <img
                                        src="{{ route("photos.preview", $photo->id) }}"
                                        alt="{{ $photo->description }}"
                                        class="rounded"
                                        style="width: 64px; height: auto"
                                    />
                                </td>
                                <td class="fw-semibold">
                                    {{ $photo->file_name ?? "—" }}
                                </td>
                                <td class="text-muted small">
                                    {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    <div class="d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
