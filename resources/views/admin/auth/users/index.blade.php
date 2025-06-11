@extends("admin.index")
@section("title", "| Utenti")

@section("content")
    @include("partials.header", ["title" => "Utenti"])
    <a
        href="{{ route("users.create") }}"
        class="btn btn-success float-end my-2"
    >
        Aggiungi Utente
    </a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Persona</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Data/Ora Creazine</th>
                    <th>Ruoli utente</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>
                            {{ $user->username === "Admin" ? "Admin" : $user->persona->nominativo }}
                        </td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format("F d, Y h:ia") }}</td>
                        <td>
                            @foreach ($user->roles as $role)
                                <span class="badge bg-success">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td>
                            <a
                                href="{{ route("users.edit", $user->id) }}"
                                class="btn btn-info pull-left"
                                style="margin-right: 3px"
                            >
                                Modifica
                            </a>
                            @if ($user->trashed())
                                <form
                                    action="{{ route("users.restore", $user->id) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method("PUT")
                                    <button
                                        type="submit"
                                        class="btn btn-warning"
                                    >
                                        Ripristina
                                    </button>
                                </form>
                            @else
                                <form
                                    action="{{ route("users.destroy", $user->id) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method("DELETE")
                                    <button
                                        type="submit"
                                        class="btn btn-danger"
                                    >
                                        Disabilita
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
