@extends("layouts.app")

@section("content")
    <div class="d-flex justify-content-center mt-5">
        <div class="card card-default">
            <div class="card-header">Entra con nome utente e password</div>
            <div class="card-body">
                <form method="POST" action="{{ route("login") }}">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            Username
                        </label>
                        <input
                            id="username"
                            type="text"
                            class="form-control"
                            name="username"
                            value="{{ old("username") }}"
                            required
                            autofocus
                        />
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            class="form-control"
                            name="password"
                            required
                        />
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" {{ old("remember") ? "checked" : "" }}  id="rememberMe"/>
                        <label class="form-check-label" for="rememberMe"> Ricordami </label>
                    </div>

                    <button type="submit" class="btn btn-primary"> Entra </button>
                </form>
            </div>
        </div>
    </div>
@endsection
