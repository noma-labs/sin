@extends("scuola.student.layout")

@section("content")
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('scuola.summary')}}">Scuola</a></li>
            <li class="breadcrumb-item"><a href="{{route("scuola.student.show", $student->id)}}">Studente</a></li>
            <li class="breadcrumb-item active" aria-current="page">Classi</li>
        </ol>
    </nav>

    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">A/S</th>
            <th scope="col">Classe</th>
            <th scope="col">Ciclo</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($classes as  $id => $class)
            <tr>
                <th scope="row">{{$id}}</th>
                <td> {{ $class->anno_scolastico }}:</td>
                <td> {{ $class->tipo_nome }}:</td>
                <td> {{ $class->tipo_ciclo }}</td>
            </tr>
            @endforeach
        </tbody>
      </table>
@endsection
