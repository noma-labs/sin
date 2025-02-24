{{-- \resources\views\roles\index.blade.php --}}
@extends("admin.index")

@section("title", "| Roles")

@section("content")
    @include("partials.header", ["title" => "Gestione ruoli"])

    <a
        href="{{ route("roles.create") }}"
        class="btn btn-success float-end my-2"
    >
        Aggiungi Ruolo
    </a>
    <div class="table-responsive col-md-8 offset-md-2">
        <table class="table table-striped">
            <thead>
                <tr class="table-warning">
                    <th>Ruolo</th>
                    <th>Descrizione</th>
                    <th>Risorse</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr class="table-primary">
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->guard_name }}</td>
                        <td>
                            @foreach ($role->permissions as $permission)
                                <span class="badge bg-success">
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

                            <form
                                action="{{ route("roles.destroy", $role->id) }}"
                                method="POST"
                                id="delete-role-{{ $role->id }}"
                            >
                                @csrf
                                @method("DELETE")
                                <button
                                    type="submit"
                                    form="delete-role-{{ $role->id }}"
                                    value="Submit"
                                    class="btn btn-danger m-1"
                                >
                                    Elimina
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
