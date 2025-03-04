@extends("admin.index")
@section("title", "| Edit User")

@section("content")
    @include("partials.header", ["title" => "Modifica utente"])
    <div class="col-lg-4 offset-md-3">
        <form action="{{ route("users.update", $user->id) }}" method="POST">
            @csrf
            @method("PUT")

            <div class="">
                <label for="persona_id">Nominativo (Persona anagrafe)(*)</label>
                <livewire:search-popolazione
                    name_input="persona_id"
                    :value="$user->persona_id"
                />
            </div>

            <div class="">
                <label for="username">Username(*)</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    class="form-control"
                    value="{{ $user->username }}"
                />
            </div>

            <div class="">
                <label for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="{{ $user->email }}"
                />
            </div>

            <div class="">
                <label for="password">Password</label>
                <br />
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                />
            </div>

            <div class="">
                <label for="password_confirmation">Conferma Password</label>
                <br />
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                />
            </div>

            <div class="row">
                <div class="col-md-8">
                    <h5><b>Assegna i ruoli all'utente</b></h5>
                    <div class="">
                        @foreach ($roles as $role)
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                {{ $user->hasRole($role) ? "checked" : "" }}
                                id="role_{{ $role->id }}"
                            />
                            <label for="role_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                            <br />
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 offset-md-1">
                    <button type="submit" class="btn btn-primary">Salva</button>
                </div>
            </div>
        </form>
    </div>
@endsection
