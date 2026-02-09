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
    <div class="d-flex gap-2 mb-3">
        <div class="btn-group" role="group" aria-label="Selettore vista">
            <a
                class="btn btn-outline-secondary {{ $currentView === "grid" ? "active" : "" }}"
                href="{{ route("photos.folders.index", array_merge(request()->except("view"), ["view" => "grid"])) }}"
            >
                Griglia
            </a>
            <a
                class="btn btn-outline-secondary {{ $currentView === "list" ? "active" : "" }}"
                href="{{ route("photos.folders.index", array_merge(request()->except("view"), ["view" => "list"])) }}"
            >
                Lista
            </a>
        </div>
    </div>

    <div class="accordion" id="photosFoldersAccordion">
        @include(
            "photo.partials.directory_node",
            [
                "node" => $dirTree,
                "prefix" => "",
                "currentView" => $currentView,
            ]
        )
    </div>

    <div class="d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
