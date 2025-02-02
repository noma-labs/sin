@extends("scuola.student.layout")

@section("content")
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Studente</a></li>
        <li class="breadcrumb-item active" aria-current="page">Elaborati</li>
        </ol>
    </nav>

    <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">A/S</th>
            <th scope="col">Titolo</th>
            <th scope="col">Classi</th>
          </tr>
        </thead>
        <tbody>
            @foreach ($works as $elaborato)
            <tr>
                <th scope="row">1</th>
                <td> {{ $elaborato->anno_scolastico }}:</td>
                <td> {{ $elaborato->titolo }}:</td>
                <td> {{ $elaborato->classi }}</td>
            </tr>
            @endforeach
        </tbody>
      </table>
@endsection
