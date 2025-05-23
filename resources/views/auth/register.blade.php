@extends("layouts.app")

<!-- @section("content") -->
@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>

                    <div class="panel-body">
                        <form method="POST" action="{{ route("register") }}">
                            @csrf

                            <div
                                class="{{ $errors->has("name") ? " has-error" : "" }}"
                            >
                                <label for="name" class="col-md-4 form-label">
                                    Name
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="name"
                                        type="text"
                                        class="form-control"
                                        name="name"
                                        value="{{ old("name") }}"
                                        required
                                        autofocus
                                    />

                                    @if ($errors->has("name"))
                                        <span class="help-block">
                                            <strong>
                                                {{ $errors->first("name") }}
                                            </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="{{ $errors->has("username") ? " has-error" : "" }}"
                            >
                                <label
                                    for="username"
                                    class="col-md-4 form-label"
                                >
                                    Username
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="username"
                                        type="text"
                                        class="form-control"
                                        name="username"
                                        value="{{ old("username") }}"
                                        required
                                        autofocus
                                    />

                                    @if ($errors->has("username"))
                                        <span class="help-block">
                                            <strong>
                                                {{ $errors->first("username") }}
                                            </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="{{ $errors->has("email") ? " has-error" : "" }}"
                            >
                                <label for="email" class="col-md-4 form-label">
                                    E-Mail Address
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="email"
                                        type="email"
                                        class="form-control"
                                        name="email"
                                        value="{{ old("email") }}"
                                        required
                                    />

                                    @if ($errors->has("email"))
                                        <span class="help-block">
                                            <strong>
                                                {{ $errors->first("email") }}
                                            </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div
                                class="{{ $errors->has("password") ? " has-error" : "" }}"
                            >
                                <label
                                    for="password"
                                    class="col-md-4 form-label"
                                >
                                    Password
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-control"
                                        name="password"
                                        required
                                    />

                                    @if ($errors->has("password"))
                                        <span class="help-block">
                                            <strong>
                                                {{ $errors->first("password") }}
                                            </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="">
                                <label
                                    for="password-confirm"
                                    class="col-md-4 form-label"
                                >
                                    Confirm Password
                                </label>

                                <div class="col-md-6">
                                    <input
                                        id="password-confirm"
                                        type="password"
                                        class="form-control"
                                        name="password_confirmation"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="">
                                <div class="col-md-6 col-md-offset-4">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Register
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
