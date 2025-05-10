@extends("layouts.app")

@section("title", "Foto show")

@section("content")
    <div class="row">
        <div class="col-md-8">
            <img src="{{ $tempFileUrl }}" alt="Photo"  class="img-fluid"/>
        </div>
        <div class="col-md-4">
            <h2>Photo Details</h2>
            <p class="mb-1">
                <strong>File Name:</strong>
                {{ $photo->file_name }}
            </p>
            <p class="mb-1">
                <strong>Folder:</strong>
                {{ $photo->folder_title }}
            </p>
            <p class="mb-0">
                <strong>Subject:</strong>
                {{ $photo->subject }}
            </p>
            <p class="mb-0">
                <strong>Persone:</strong>
                <ul>
                    @foreach ($people as $person)
                        <li><strong>{{ $person->persona_nome }} </strong>
                            @if ($person->id !== null)
                                <a href="{{ route("nomadelfia.person.show", $person->id) }}" >
                                    {{ $person->NOME }} {{ $person->COGNOME }}
                                </a>
                            @else
                                {{ $person->NOME }} {{ $person->COGNOME }}
                              @endif

                        </li>
                    @endforeach
                </ul>
            </p>
            <p class="mb-0">


                <form
                    action="{{ route("photos.update", $photo->sha) }}"
                    method="POST"
                    class="mt-2"
                >
                    @csrf
                    @method("PUT")
                    <label for="description" class="form-label">Description</label>
                    <textarea type="text" name="description" class="form-control">{{ $photo->description }}</textarea>

                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ $photo->location }}">
                    <button
                        type="submit"
                        class="btn btn-secondary"
                    >
                        Save
                    </button>
            </form>
            </p>

        </div>
    </div>
@endsection
