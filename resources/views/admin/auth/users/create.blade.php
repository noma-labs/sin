@extends("admin.index")
@section("title", "| Add User")

@section("content")
    @include("partials.header", ["title" => "Aggiungi utente"])
    <div class="col-lg-4 offset-md-3">
        {{ Form::open(["method" => "POST", "route" => "users.store"]) }}
        <div class="form-group">
            {{ Form::label("name", "Nominativo (Persona anagrafe)(*)") }}
            <livewire:search-popolazione
                name_input="persona_id"
                :multiple="false"
            />
        </div>

        <div class="form-group">
            {{ Form::label("username", "Username(*)") }}
            {{ Form::text("username", "", ["class" => "form-control"]) }}
        </div>

        <div class="form-group">
            <h5><b>Assegna i ruoli all'utente</b></h5>
            @foreach ($roles as $role)
                {{ Form::checkbox("roles[]", $role->id) }}
                {{ Form::label($role->name, ucfirst($role->nome)) }}
                <br />
            @endforeach
        </div>

        <div class="form-group">
            {{ Form::label("password", "Password(*)") }}
            <br />
            {{ Form::password("password", ["class" => "form-control"]) }}
        </div>

        <div class="form-group">
            {{ Form::label("password", "Conferma Password(*)") }}
            <br />
            {{ Form::password("password_confirmation", ["class" => "form-control"]) }}
        </div>
        <p class="text-danger">(*) campi obbligatori</p>
        {{ Form::submit("Aggiungi", ["class" => "btn btn-primary"]) }}

        {{ Form::close() }}
    </div>
@endsection
