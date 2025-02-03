@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Alunno: " . $student->nome . " " . $student->cognome])

    <ul
        class="nav nav-pills pb-3 flex-column flex-sm-row justify-content-center"
    >
        <a
            class="nav-link"
            aria-current="page"
            href="{{ route("scuola.student.show", $student->id) }}"
        >
            Anagrafica
        </a>
        <a
            class="nav-link active"
            href="{{ route("scuola.student.works.show", $student->id) }}"
        >
            Elaborati
        </a>
        <a
            class="nav-link"
            href="{{ route("scuola.student.classes.show", $student->id) }}"
        >
            Classi
        </a>
    </ul>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th>Anno scolastico</th>
                <th scope="col">Titolo</th>
                <th scope="col">Classi</th>
                <th scope="col">Operazioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($works as $elaborato)
                <tr>
                    <th scope="row">1</th>
                    <td>
                        <span class="badge rounded-pill text-bg-secondary">
                            {{ $elaborato->anno_scolastico }}
                        </span>
                    </td>
                    <td>{{ $elaborato->titolo }}</td>
                    <td>{{ $elaborato->classi }}</td>
                    <td>
                        <a
                            href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                            class="btn btn-primary"
                        >
                            Visualizza
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
