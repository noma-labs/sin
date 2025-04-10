@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Prestiti Cliente"])

    <div class="row">
        <div class="col-md-12">
            <p class="h3">
                Cliente:
                <strong>{{ $cliente->nominativo }}</strong>
            </p>
            <p class="h5">
                Prestiti attivi:
                <strong>{{ $prestitiAttivi->count() }}</strong>
            </p>
        </div>
        <div class="col-md-8">
            @if ($prestitiAttivi->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr class="table-warning">
                                <th>Inizio prestito</th>
                                <th>Collocazione</th>
                                <th>Titolo</th>
                                <th>Note</th>
                                <th>Operazioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($prestitiAttivi as $prestito)
                                <tr class="table-primary" hoverable>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ $prestito->data_inizio_prestito }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $prestito->libro->collocazione }}
                                    </td>
                                    <td>
                                        {{ $prestito->libro->titolo }}
                                    </td>
                                    <td>
                                        {{ $prestito->note }}
                                    </td>
                                    <td>
                                        <a
                                            class="btn btn-warning"
                                            href="{{ route("books.loans.show", ["id" => 1]) }}"
                                            role="button"
                                        >
                                            Dettaglio prestito
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="bg-danger">Nessuna prenotazione attiva</p>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-success my-2">
                <div class="card-header">
                    Storico Prestiti ({{ $prestitiRestituiti->count() }})
                </div>
                <div class="card-body">
                    @if ($prestitiRestituiti->count() > 0)
                        <ul class="list-group">
                            @foreach ($prestitiRestituiti as $prestito)
                                <li class="list-group-item">
                                    <a
                                        href="{{ route("books.loans.show", [$prestito->id]) }}"
                                    >
                                        {{ $prestito->libro->collocazione }}-
                                        {{ $prestito->libro->titolo }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="bg-danger">Nessun prestito restituito.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
