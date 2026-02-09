@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2 my-3">
        @foreach ($years as $year)
            <a
                href="{{ route("photos.folders.index", ["year" => $year->year, "name" => request("name"), "view" => request("view", "grid")]) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @endforeach
    </div>

    @php($currentView = $currentView ?? request("view", "grid"))

    @php($children = isset($dirTree["children"]) ? $dirTree["children"] : [])
    <div class="row row-cols-2 row-cols-md-5 g-3">
        @php($sorted = collect($children)->sortBy(function ($child) {return is_string($child["label"] ?? null) ? $child["label"] : "";})->all())
        @php($seen = [])
        @foreach ($sorted as $label => $child)
            @php($folderLabel = is_string($child["label"] ?? null) ? $child["label"] : (is_string($label) ? $label : ""))
            @php($dedupeKey = $folderLabel)
            @if (isset($seen[$dedupeKey]))
                @continue
            @endif

            @php($seen[$dedupeKey] = true)
            @php($firstPhoto = $child["preview"] ?? (isset($child["photos"]) && count($child["photos"]) ? $child["photos"][0] : null))
            <div class="col">
                <a
                    href="{{ route("photos.folders.show", ["path" => $folderLabel, "view" => $currentView]) }}"
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
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
