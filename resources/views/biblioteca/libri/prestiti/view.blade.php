@extends("biblioteca.libri.index")

@section("content")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="mr-auto p-2">
                <span class="h1 text-center">Gestione prestiti</span>
            </div>
            <div class="p-2 text-right">
                <h5 class="m-1">
                    {{ App\Biblioteca\Models\Prestito::InPrestito()->count() }}
                    prestiti attivi/
                    {{ App\Biblioteca\Models\Prestito::count() }} prestiti
                    totali.
                </h5>
            </div>
        </div>
    </div>

    <form
        method="GET"
        class="form"
        action="{{ route("libri.prestiti.ricerca") }}"
    >
        <div class="row">
            {{ csrf_field() }}
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Cliente</label>
                    <autocomplete
                        placeholder="Inserisci nominativo..."
                        name="persona_id"
                        url="{{ route("api.nomadeflia.persone.search") }}"
                    ></autocomplete>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="xIdBibliotecario" class="control-label">
                        Bibliotecario
                    </label>
                    <select
                        class="form-control"
                        name="xIdBibliotecario"
                        type="text"
                        id="xIdBibliotecario"
                    >
                        <option value="">
                            --- Seleziona il bibliotecario--
                        </option>
                        @foreach ($bibliotecari as $bibliotecario)
                            <option value="{{ $bibliotecario->persona_id }}">
                                {{ $bibliotecario->nominativo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <search-collocazione
                    placeholder="Ins. lettere..."
                    url-lettere="{{ route("api.biblioteca.collocazione") }}"
                    name="collocazione"
                    numeri-mancanti="false"
                    numero-nuovo="false"
                ></search-collocazione>
            </div>
            <div class="col-md-4">
                <label class="control-label">Titolo</label>
                <autocomplete
                    placeholder="Inserisci titolo ..."
                    name="titolo"
                    url="{{ route("api.biblioteca.titolo") }}"
                ></autocomplete>
            </div>
            <div class="col-md-4">
                <label class="control-label">Note</label>
                <input class="form-control" type="text" name="note" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="xInizioPrestito" class="control-label">
                        Data Inizio Prestito
                    </label>
                    <select
                        class="form-control"
                        name="xSegnoInizioPrestito"
                        type="text"
                    >
                        <option value="">--- Seleziona criterio---</option>
                        <option value="<">Minore</option>
                        <option value="<=">Minore Uguale</option>
                        <option value="=">Uguale</option>
                        <option value=">">Maggiore</option>
                        <option value=">=">Maggiore Uguale</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="xInizioPrestito">&nbsp;</label>
                    <input
                        class="form-control"
                        name="xInizioPrestito"
                        type="date"
                        id="datep"
                        size="10"
                        maxlength="10"
                        placeholder="Inserisci data inizio prestito"
                    />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="xFinePrestito" class="control-label">
                        Data Fine Prestito
                    </label>
                    <select class="form-control" name="xSegnoFinePrestito">
                        <option value="">---Seleziona criterio---</option>
                        <option value="<">Minore</option>
                        <option value="<=">Minore Uguale</option>
                        <option value="=">Uguale</option>
                        <option value=">">Maggiore</option>
                        <option value=">=">Maggiore Uguale</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="xFinePrestito">&nbsp;</label>
                    <input
                        class="form-control"
                        name="xFinePrestito"
                        type="date"
                        id="dater"
                        size="20"
                        maxlength="100"
                        placeholder="Inserisci data fine prestito"
                    />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="float-left my-2">
                    <a
                        class="btn btn-primary"
                        href="{{ route("libri.ricerca") }}"
                        role="button"
                    >
                        Torna alla ricerca dei libri
                    </a>
                </div>
                <div class="float-right m-1">
                    <!-- <button  class="btn btn-info"   name="_concludi" value="true"  type="submit">Elimina prestito</button> -->
                    <button
                        class="btn btn-success"
                        name="biblioteca"
                        type="submit"
                    >
                        Cerca Prestiti
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="alert alert-info">
        Ricerca effettuata:
        <strong>{{ $msgSearch }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">
            &times;
        </a>
    </div>

    <!-- <div class="alert alert-info alert-dismissable "><strong> {{ $query }}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  </div> -->

    <div id="results" class="alert alert-success">
        Prestiti trovati:
        <strong>{{ $prestiti->count() }}</strong>
    </div>

    @if ($prestiti->count() > 0)
        <table class="table table-hover table-striped table-bordered">
            <thead class="thead-inverse">
                <tr>
                    <th style="width: 15%">DATA INIZIO PRESTITO</th>
                    <th style="width: 10%">CLIENTE</th>
                    <th style="width: 15%">BIBLIOTECARIO</th>
                    <th style="width: 30%">TITOLO</th>
                    <th style="width: 10%">COLLOC.</th>
                    <th style="width: 20%">NOTE</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prestiti as $prestito)
                    <tr>
                        <td
                            onclick="gotoPrestitoDetails({{ $prestito->id }})"
                            width="20"
                        >
                            {{ $prestito->data_inizio_prestito }}

                            @if ($prestito->in_prestito)
                                <span class="badge badge-warning">
                                    {{ Carbon\Carbon::parse($prestito->data_inizio_prestito)->diffInDays(Carbon\Carbon::now(), false) }}
                                    giorni
                                </span>
                            @else
                                <span class="badge badge-success">
                                    Restituito
                                </span>
                            @endif
                        </td>
                        <td onclick="gotoPrestitoDetails({{ $prestito->id }})">
                            {{ $prestito->cliente->nominativo }}
                        </td>
                        <td onclick="gotoPrestitoDetails({{ $prestito->id }})">
                            {{ $prestito->bibliotecario->nominativo }}
                        </td>
                        <td onclick="gotoPrestitoDetails({{ $prestito->id }})">
                            {{ $prestito->libro->titolo }}
                        </td>
                        <td onclick="gotoPrestitoDetails({{ $prestito->id }})">
                            {{ $prestito->libro->collocazione }}
                        </td>
                        <td onclick="gotoPrestitoDetails({{ $prestito->id }})">
                            {{ $prestito->note }}
                        </td>
                    </tr>
                @empty
                    <div class="alert alert-danger">
                        <strong>Nessun risultato ottenuto</strong>
                    </div>
                @endforelse
            </tbody>
        </table>
    @endif
@endsection
