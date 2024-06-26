@extends("layouts.app")

<!-- @section("content") -->
@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-default">
                    <div class="card-header">
                        Entra con nome utente e password
                    </div>
                    <div class="card-body">
                        <form
                            class="form-horizontal"
                            method="POST"
                            action="{{ route("login") }}"
                        >
                            {{ csrf_field() }}
                            <div
                                class="form-group{{ $errors->has("username") ? " has-error" : "" }}"
                            >
                                <label
                                    for="username"
                                    class="col-md-4 control-label"
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
                                class="form-group{{ $errors->has("password") ? " has-error" : "" }}"
                            >
                                <label
                                    for="password"
                                    class="col-md-4 control-label"
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

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="remember"
                                                {{ old("remember") ? "checked" : "" }}
                                            />
                                            Ricordami
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Entra
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
