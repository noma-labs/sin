@extends("photo.main")

@section("title", "Foto – " . $photo->file_name)

@section("content")
    <div class="row g-4">
        {{-- Photo --}}
        <div class="col-md-8">
            <img
                src="{{ route("photos.preview", ["id" => $photo->id, "draw_faces" => true]) }}"
                alt="{{ $photo->file_name }}"
                class="img-fluid rounded"
            />
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4 d-flex flex-column gap-3">

            {{-- Photo info --}}
            <div class="card">
                <div class="card-header fw-semibold">Foto</div>
                <div class="card-body p-0">
                    <dl class="row g-0 mb-0 px-3 py-2">
                        <dt class="col-5 text-muted small py-1">Data</dt>
                        <dd class="col-7 py-1 mb-0">
                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "—" }}
                        </dd>
                        <dt class="col-5 text-muted small py-1">File</dt>
                        <dd class="col-7 py-1 mb-0">
                            <span class="text-break small">{{ $photo->file_name }}</span>
                        </dd>
                        <dt class="col-5 text-muted small py-1">Percorso</dt>
                        <dd class="col-7 py-1 mb-0">
                            <span class="text-break small text-muted">{{ $photo->source_file }}</span>
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Stripe info --}}
            @if ($stripe)
                <div class="card">
                    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                        Striscia
                        <a
                            href="{{ route("photos.stripes.show", $stripe->id) }}"
                            target="_blank"
                            class="btn btn-sm btn-outline-secondary py-0"
                        >
                            {{ $stripe->datnum }} ↗
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <dl class="row g-0 mb-0 px-3 py-2">
                            <dt class="col-5 text-muted small py-1">Fonte</dt>
                            <dd class="col-7 py-1 mb-0">{{ $stripe->source }}</dd>
                            @if ($stripe->data)
                                <dt class="col-5 text-muted small py-1">Data</dt>
                                <dd class="col-7 py-1 mb-0">{{ $stripe->data }}</dd>
                            @endif
                            @if ($stripe->localita)
                                <dt class="col-5 text-muted small py-1">Luogo</dt>
                                <dd class="col-7 py-1 mb-0">{{ $stripe->localita }}</dd>
                            @endif
                            @if ($stripe->argomento)
                                <dt class="col-5 text-muted small py-1">Argomento</dt>
                                <dd class="col-7 py-1 mb-0">{{ $stripe->argomento }}</dd>
                            @endif
                            @if ($stripe->descrizione)
                                <dt class="col-5 text-muted small py-1">Descrizione</dt>
                                <dd class="col-7 py-1 mb-0">{{ $stripe->descrizione }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
            @endif

            {{-- People --}}
            @if ($people->isNotEmpty())
                <div class="card">
                    <div class="card-header fw-semibold">Persone</div>
                    <div class="card-body d-flex flex-wrap gap-1">
                        @foreach ($people as $person)
                            <a
                                href="{{ route("photos.index", ["name" => $person->persona_nome]) }}"
                                class="btn btn-sm btn-outline-secondary"
                            >
                                {{ $person->persona_nome }}
                                @if ($person->id !== null)
                                    ({{ Illuminate\Support\Str::title($person->nome) }}
                                    {{ Illuminate\Support\Str::title($person->cognome) }})
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            @can("photo.download")
                <a
                    href="{{ route("photos.download", $photo->id) }}"
                    class="btn btn-outline-secondary"
                >
                    Download
                </a>
            @endcan

        </div>
    </div>
@endsection
