@extends("photo.main")

@section("title", "Persone")

@section("content")
    <div class="d-flex justify-content-end m-3">
        <form
            method="GET"
            action="{{ route("photos.face.index") }}"
        >
            <div class="input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Cerca persona..."
                    value="{{ request("search") }}"
                />
                <button class="btn btn-primary" type="submit">Cerca</button>
            </div>
        </form>
    </div>

    <div class="d-flex flex-wrap gap-2">
        @foreach ($faces as $face)
            <a
                href="{{ route("photos.index", ["name" => $face->name]) }}"
                class="btn btn-sm btn-outline-secondary mb-1"
            >
                @if ($face->id !== null)
                    {{ Illuminate\Support\Str::title($face->nome) }}
                    {{ Illuminate\Support\Str::title($face->cognome) }}
                @else
                    {{ $face->name }}
                @endif
                <span class="badge text-bg-secondary px-2 ms-1">
                    {{ $face->count }}
                </span>
            </a>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $faces->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
    </div>
@endsection
