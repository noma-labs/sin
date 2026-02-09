@extends("photo.main")

@section("content")
    @php($segments = array_values(array_filter(explode("/", $path), fn ($s) => $s !== "")))
    <nav aria-label="Percorso" class="mb-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a
                    href="{{ route("photos.folders.index", array_merge(request()->except("view"), ["view" => $currentView])) }}"
                >
                    Cartelle
                </a>
            </li>
            @php($crumbPath = "")
            @foreach ($segments as $i => $seg)
                @php($crumbPath = $crumbPath === "" ? $seg : $crumbPath . "/" . $seg)

                @if ($i + 1 < count($segments))
                    <li class="breadcrumb-item">
                        <a
                            href="{{ route("photos.folders.show", ["path" => $crumbPath, "view" => $currentView]) }}"
                        >
                            {{ $seg }}
                        </a>
                    </li>
                @else
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $seg }}
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
    @php($children = isset($dirTree["children"]) ? $dirTree["children"] : [])
    @php($hasChildren = count($children) > 0)
    @if (! $hasChildren && $photos->total() > 0)
        <div class="d-flex gap-2 mb-3">
            <div class="btn-group" role="group" aria-label="Selettore vista">
                <a
                    class="btn btn-outline-secondary {{ $currentView === "grid" ? "active" : "" }}"
                    href="{{ route("photos.folders.show", ["view" => "grid", "path" => $path]) }}"
                >
                    Griglia
                </a>
                <a
                    class="btn btn-outline-secondary {{ $currentView === "list" ? "active" : "" }}"
                    href="{{ route("photos.folders.show", ["view" => "list", "path" => $path]) }}"
                >
                    Lista
                </a>
            </div>
        </div>
    @endif

    @php($children = isset($dirTree["children"]) ? $dirTree["children"] : [])
    @php($hasChildren = count($children) > 0)
    @if ($hasChildren)
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
                        href="{{ route("photos.folders.show", ["path" => $path . "/" . $folderLabel, "view" => $currentView]) }}"
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
    @else
        @if ($currentView === "grid")
            <div class="d-flex flex-wrap">
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

                                    @if ($photo->strip)
                                        <span class="badge text-bg-success">
                                            {{ $photo->strip->datnum }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-danger">
                                            Senza Striscia
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </figure>
                    </a>
                @endforeach
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Anteprima</th>
                            <th>Nome File</th>
                            <th>Data</th>
                            <th>Striscia</th>
                            <th>Persone</th>
                            <th></th>
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
                                <td>
                                    @if ($photo->strip)
                                        <span class="badge text-bg-success">
                                            {{ $photo->strip->datnum }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-danger">
                                            Senza Striscia
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($photo->subjects)
                                        <span
                                            class="small text-truncate d-inline-block"
                                            style="max-width: 40ch"
                                        >
                                            {{ $photo->subjects }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a
                                        href="{{ route("photos.show", $photo->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Apri
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    <div class="d-flex justify-content-center">
        {{ $photos->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
