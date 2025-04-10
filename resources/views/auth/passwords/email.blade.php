@extends("layouts.app")

<!-- @section("content") -->
@section("content")
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Reset Password</div>

                    <div class="panel-body">
                        @if (session("status"))
                            <div class="alert alert-success">
                                {{ session("status") }}
                            </div>
                        @endif

                        <form
                            method="POST"
                            action="{{ route("password.email") }}"
                        >
                            @csrf

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

                            <div class="">
                                <div class="col-md-6 col-md-offset-4">
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Send Password Reset Link
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
