{{-- \resources\views\roles\index.blade.php --}}
@extends("admin.index")

@section("title", "| Roles")

@section("content")
    @include("partials.header", ["title" => "Gestione ruoli"])

    <a
        href="{{ route("roles.create") }}"
        class="btn btn-success float-right my-2"
    >
        Aggiungi Ruolo
    </a>
    <div class="table-responsive col-md-8 offset-md-2">
        <table class="table table-bordered table-striped">
            <thead class="thead-inverse">
                <tr>
                    <th>Ruolo</th>
                    <th>Descrizione</th>
                    <th>Risorse</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->guard_name }}</td>
                        <td>
                            @foreach ($role->permissions as $permission)
                                <span class="badge badge-success">
                                    {{ $permission->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <a
                                href="{{ route("roles.edit", $role->id) }}"
                                class="btn btn-info"
                            >
                                Modifica
                            </a>
                            <button
                                type="submit"
                                form="{{ $role->id }}"
                                value="Submit"
                                ,
                                class="btn btn-danger m-1"
                            >
                                Elimina
                            </button>
                            {!! Form::open(["method" => "DELETE", "id" => $role->id, "route" => ["roles.destroy", $role->id]]) !!}
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
