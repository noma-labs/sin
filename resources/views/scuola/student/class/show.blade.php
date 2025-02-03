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
            class="nav-link"
            href="{{ route("scuola.student.works.show", $student->id) }}"
        >
            Elaborati
        </a>
        <a
            class="nav-link active"
            href="{{ route("scuola.student.classes.show", $student->id) }}"
        >
            Classi
        </a>
    </ul>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Anno Scolastico</th>
                <th scope="col">Ciclo</th>
                <th scope="col">Classe</th>
                <th scope="col">Operazioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($classes as $id => $class)
                <tr>
                    <th scope="row">{{ $id }}</th>
                    <td>
                        <span class="badge rounded-pill text-bg-secondary">
                            {{ $class->anno_scolastico }}
                        </span>
                    </td>
                    <td>{{ $class->tipo_ciclo }}</td>
                    <td>{{ $class->tipo_nome }}:</td>
                    <td>
                        <a
                            href="{{ route("scuola.classi.show", $class->id) }}"
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
