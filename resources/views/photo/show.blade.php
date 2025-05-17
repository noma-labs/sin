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
            <h2>Photo Details</h2>

            <p class="mb-1">
                <strong>Data:</strong>
                {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
            </p>

            <p class="mb-1">
                <strong>File Name:</strong>
                {{ $photo->file_name }}
            </p>
            <p class="mb-1">
                <strong>Folder:</strong>
                {{ $photo->directory }}
            </p>
            <div class="mb-3">
                <strong>Persone:</strong>
                <ul>
                    @foreach ($people as $person)
                        <li>
                            <strong>{{ $person->persona_nome }}</strong>
                            @if ($person->id !== null)
                                <a
                                    href="{{ route("nomadelfia.person.show", $person->id) }}"
                                >
                                    {{ Illuminate\Support\Str::title($person->NOME) }}
                                    {{ Illuminate\Support\Str::title($person->COGNOME) }}
                                    @if ($person->ALIAS)
                                        <span class="text-muted">
                                            ({{ Illuminate\Support\Str::title($person->ALIAS) }})
                                        </span>
                                    @endif
                                </a>
                            @else
                                {{ Illuminate\Support\Str::title($person->NOME) }}
                                {{ Illuminate\Support\Str::title($person->COGNOME) }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mb-3">
                <form
                    action="{{ route("photos.update", $photo->id) }}"
                    method="POST"
                    class="mt-2"
                >
                    @csrf
                    @method("PUT")
                    <input
                        type="datetime"
                        name="taken_at"
                        value="{{ $photo->taken_at }}"
                        class="form-control"
                    />

                    <label for="description" class="form-label">
                        Description
                    </label>
                    <textarea
                        type="text"
                        name="description"
                        class="form-control"
                    >
{{ $photo->description }}</textarea
                    >

                    <label for="location" class="form-label">Location</label>
                    <input
                        type="text"
                        name="location"
                        class="form-control mb-3"
                        value="{{ $photo->location }}"
                    />
                    <button type="submit" class="btn btn-secondary">
                        Save
                    </button>
                </form>

                <a
                    href="{{ route("photos.download", $photo->id) }}"
                    class="btn btn-secondary"
                >
                    Download
                </a>
            </div>
        </div>
    </div>
@endsection
