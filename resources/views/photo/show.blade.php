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

            @if ($stripe)
                <p class="mb-1">
                    <strong>Striscia:</strong>
                    <a href="{{ route("photos.stripes.show", $stripe->id) }}" target="_blank" class="text-decoration-none">
                        {{ $stripe->datnum }} ({{ $stripe->source }})
                    </a>
                </p>
                @if ($stripe->descrizione)
                    <p class="mb-1">
                        <strong>Descrizione:</strong>
                        {{ $stripe->descrizione }}
                    </p>
                @endif
                @if ($stripe->localita)
                    <p class="mb-1">
                        <strong>Luogo:</strong>
                        {{ $stripe->localita }}
                    </p>
                @endif
            @endif

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
