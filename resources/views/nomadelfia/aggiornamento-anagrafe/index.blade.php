@extends("nomadelfia.index")

@section("archivio")
    @include("partials.header", ["title" => "Aggiornamento Anagrafe"])

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Evento</th>
                <th scope="col">Persona</th>
                <th scope="col">Dettaglio</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($activity as $a)
                <tr>
                    <th scope="row">
                        {{ $loop->iteration }}
                    </th>
                    <td>
                        @if ($a->isEnterEvent())
                            <span class="badge badge-success">Entrata</span>
                        @endif

                        @if ($a->isExitEvent())
                            <span class="badge badge-danger">Uscita</span>
                        @endif

                        @if ($a->isDeathEvent())
                            <span class="badge badge-dark">Decesso</span>
                        @endif
                    </td>
                    <td>
                        @include("nomadelfia.templates.persona", ["persona" => $a->subject])
                    </td>
                    <td>{{ $a->properties }}</td>
                </tr>
            @empty
                <tr>
                    <th scope="row">Nono ci sono attività recenti</th>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
