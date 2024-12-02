@extends("admin.index")
@section("title", "| Aggiungi Ruolo")

@section("content")
    @include("partials.header", ["title" => "Aggiugni ruolo"])

    <form method="post" action="{{ route("roles.store") }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form-group">
                    <label for="nome">Nome del ruolo</label>
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        class="form-control"
                    />
                </div>
                <div class="form-group">
                    <label for="descrizione">Descrizione ruolo</label>
                    <input
                        type="text"
                        name="descrizione"
                        id="descrizione"
                        class="form-control"
                    />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h4><strong>Associa i permessi</strong></h4>
                <table class="table table-bordered table-hover table-sm">
                    <tbody>
                        @foreach ($permissions as $permission)
                            <tr>
                                <input
                                    type="checkbox"
                                    id="{{ $permission->id }}"
                                    name="{{ $permission->name }}"
                                    value="{{ $permission->id }}"
                                />
                                <label for="{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                                <br />
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Aggiungi</button>
            </div>
        </div>
    </form>
@endsection
