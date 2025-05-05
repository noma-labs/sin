@extends("layouts.app")

@section("title", "Foto show")

@section("content")
    <div class="row justify-content-around">
        <div class="col-md-8">
            <img src="{{ tempFilePath }}" />
        </div>
        <div class="col-md-4">
            <h2>Photo Details</h2>
        </div>
    </div>
@endsection
