@extends("photo.main")

@section("content")
    <div class="row">
        <div class="col-md-8">
            <img
                src="{{ route("photos.preview", ["id" => $photo->id, "draw_faces" => true]) }}"
                alt="Photo"
                class="img-fluid"
            />
        </div>
        <div class="col-md-4">
            <h2>Dettaglio</h2>

            <p class="mb-1">
                <strong>Data:</strong>
                {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
            </p>

            <p class="mb-1">
                <strong>File:</strong>
                {{ $photo->source_file }}
            </p>
            <div class="mb-3">
                <p class="fw-bold">Persone:</p>
                @foreach ($people as $person)
                    <a
                        href="{{ route("photos.index", ["name" => $person->persona_nome]) }}"
                        class="btn btn-sm btn-outline-secondary mb-1"
                    >
                        {{ $person->persona_nome }}
                        @if ($person->id !== null)
                                ({{ Illuminate\Support\Str::title($person->nome) }}
                                {{ Illuminate\Support\Str::title($person->cognome) }})
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="mb-3">
                <form
                    action="{{ route("photos.update", $photo->id) }}"
                    method="POST"
                    class="mt-2"
                >
                    @csrf
                    @method("PUT")
                    <label for="taken_at" class="form-label">
                        Data di scatto
                    </label>
                    <input
                        type="datetime"
                        name="taken_at"
                        value="{{ $photo->taken_at }}"
                        class="form-control"
                    />

                    <label for="description" class="form-label">
                        Descrizione
                    </label>
                    <textarea
                        type="text"
                        name="description"
                        class="form-control"
                    >
{{ $photo->description }}</textarea
                    >

                    <label for="location" class="form-label">Luogo</label>

                    @can("photo.update")
                        <input
                            type="text"
                            name="location"
                            class="form-control mb-3"
                            value="{{ $photo->location }}"
                        />
                        <button type="submit" class="btn btn-secondary">
                            Salva
                        </button>
                    @endcan
                </form>

                @can("photo.download")
                    <a
                        href="{{ route("photos.download", $photo->id) }}"
                        class="btn btn-secondary"
                    >
                        Download
                    </a>
                @endcan
            </div>
        </div>
    </div>
@endsection
