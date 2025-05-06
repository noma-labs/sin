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
        </div>
    </div>
@endsection
