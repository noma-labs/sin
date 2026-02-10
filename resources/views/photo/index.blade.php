@extends("photo.main")

@section("content")
    <div class="d-flex flex-wrap gap-2 my-3">
        @foreach ($years as $year)
            <a
                href="{{ route("photos.index", ["year" => $year->year, "name" => request("name"), "view" => request("view", "grid")]) }}"
                class="btn btn-sm btn-outline-secondary"
            >
                {{ $year->year }}
                <span class="badge text-bg-secondary">
                    {{ $year->count }}
                </span>
            </a>
        @endforeach
    </div>

    @php($currentView = request("view", "grid"))

    <div class="d-flex gap-2 mb-3">
        <div class="btn-group" role="group" aria-label="Selettore vista">
            <a
                class="btn btn-outline-secondary {{ $currentView === "grid" ? "active" : "" }}"
                href="{{ route("photos.index", array_merge(request()->except("view"), ["view" => "grid"])) }}"
            >
                Griglia
            </a>
            <a
                class="btn btn-outline-secondary {{ $currentView === "list" ? "active" : "" }}"
                href="{{ route("photos.index", array_merge(request()->except("view"), ["view" => "list"])) }}"
            >
                Lista
            </a>
        </div>
    </div>

    <a
        href="{{ route("photos.slideshow", request()->all()) }}"
        class="btn btn-primary mb-3"
        target="_blank"
    >
        Slideshow
    </a>

    @if ($currentView === "grid")
        <div class="d-flex flex-wrap">
            @foreach ($photos as $photo)
                <a href="{{ route("photos.show", $photo->id) }}">
                    <figure class="figure m-1" style="width: 20rem">
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
                                <div class="small">
                                    {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                </div>
                                <div class="small">
                                    {{ $photo->subjects ? $photo->subjects : "" }}
                                </div>
                            </div>
                        </div>
                    </figure>
                </a>
            @endforeach
        </div>
    @elseif ($currentView === "list")
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead>
                    <tr>
                        <th>Anteprima</th>
                        <th>Nome File</th>
                        <th>Data</th>
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

    <div class="d-flex justify-content-center">
        {{ $photos->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
