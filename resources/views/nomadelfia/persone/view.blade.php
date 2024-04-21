@extends("biblioteca.libri.index")
@section("content")
    <div class="row">
        <div class="pull-right">
            <h4>
                <strong>{{ ViewClientiBiblioteca::count() }}</strong>
                Clienti
            </h4>
        </div>
    </div>

    @include("partials.header", ["title" => "Ricerca Cliente"])

    <div class="alert alert-info alert-dismissable fade in">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">
            &times;
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-condensed">
            <thead class="thead-inverse">
                <tr>
                    <th>NOMINATIVO.</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clienti as $cliente)
                    <tr>
                        <td
                            onclick="gotoClienteDetails({{ $cliente->id }} )"
                            width="10"
                        >
                            {{ $cliente->nominativo }}
                        </td>
                    </tr>
                @empty
                    <div class="alert alert-danger">
                        <strong>Nessun risultato ottenuto</strong>
                    </div>
                @endforelse
            </tbody>
        </table>
    </div>

    <button class="btn btn-success" name="inizio" onClick="toTop()">
        INIZIO
    </button>
@endsection
