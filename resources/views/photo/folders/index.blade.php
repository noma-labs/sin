@extends("photo.main")

@section("content")
    <div class="row row-cols-2 row-cols-md-5 g-3">
        @foreach ($dirTree as $child)
            <div class="col">
                <a
                    href="{{ route("photos.folders.show", ["path" => $child["dirName"]]) }}"
                    class="text-decoration-none"
                >
                    <div class="card h-100 shadow-sm">
                        @if ($child["preview"] ?? null)
                            <img
                                src="{{ route("photos.preview", $child["preview"]->id) }}"
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
                                    {{ $child["dirName"] }}
                                </h6>
                                @if (isset($child["total"]))
                                    <span class="badge text-bg-secondary">
                                        {{ $child["total"] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
