@extends("admin.index")
@section("title", "| Add User")

@section("content")
    @include("partials.header", ["title" => "Aggiungi utente"])
    <div class="col-lg-4 offset-md-3">
        <form action="{{ route("users.store") }}" method="POST">
            @csrf

            <div class="">
                <label for="persona_id">Nominativo (Persona anagrafe)(*)</label>
                <livewire:search-popolazione name_input="persona_id" />
            </div>

            <div class="">
                <label for="username">Username(*)</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    class="form-control"
                />
            </div>

            <div class="">
                <h5><b>Assegna i ruoli all'utente</b></h5>
                @foreach ($roles as $role)
                    <input
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->name }}"
                        id="role_{{ $role->id }}"
                    />
                    <label for="role_{{ $role->id }}">
                        {{ ucfirst($role->name) }}
                    </label>
                    <br />
                @endforeach
            </div>

            <div class="">
                <label for="password">Password(*)</label>
                <br />
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                />
            </div>

            <div class="">
                <label for="password_confirmation">Conferma Password(*)</label>
                <br />
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                />
            </div>

            <p class="text-danger">(*) campi obbligatori</p>
            <button type="submit" class="btn btn-primary">Aggiungi</button>
        </form>
    </div>
@endsection
