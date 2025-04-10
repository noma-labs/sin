@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Gestione prestiti", "subtitle" => App\Biblioteca\Models\Prestito::InPrestito()->count() . "/" . App\Biblioteca\Models\Prestito::count() . " (attivi/totali)"])

    <form method="GET" class="form" action="{{ route("books.loans.search") }}">
        <div class="row">
            @csrf
            <div class="col-md-6">
                <div class="">
                    <label class="form-label">Cliente</label>
                    <livewire:search-popolazione name_input="persona_id" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="">
                    <label for="xIdBibliotecario" class="form-label">
                        Bibliotecario
                    </label>
                    <livewire:search-persona name_input="xIdBibliotecario" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Collocazione -lettere</label>
                        <livewire:search-collocazione-lettere />
                    </div>
                    <div class="col-md-4">
                        <livewire:search-collocazione-numeri
                            :show-free="false"
                            :show-busy="true"
                            :show-next="false"
                            name="collocazione"
                        />
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label">Titolo</label>
                <input class="form-control" type="text" name="titolo" />
            </div>
            <div class="col-md-4">
                <label class="form-label">Note</label>
                <input class="form-control" type="text" name="note" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="">
                    <label for="xInizioPrestito" class="form-label">
                        Data Inizio Prestito
                    </label>
                    <select
                        class="form-select"
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
                <div class="">
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
                <div class="">
                    <label for="xFinePrestito" class="form-label">
                        Data Fine Prestito
                    </label>
                    <select class="form-select" name="xSegnoFinePrestito">
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
                <div class="">
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
                <div class="float-start my-2">
                    <a
                        class="btn btn-primary"
                        href="{{ route("books.index") }}"
                        role="button"
                    >
                        Torna alla ricerca dei libri
                    </a>
                </div>
                <div class="float-end m-1">
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
        <a
            href="#"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="close"
        >
            &times;
        </a>
    </div>

    <!-- <div class="alert alert-info alert-dismissable "><strong> {{ $query }}</strong>
        <a href="#" class="btn-close" data-bs-dismiss="alert" aria-label="close">&times;</a>
  </div> -->

    <div id="results" class="alert alert-success">
        Prestiti trovati:
        <strong>{{ $prestiti->count() }}</strong>
    </div>

    @if ($prestiti->count() > 0)
        <table class="table table-hover table-striped">
            <thead>
                <tr class="table-warning">
                    <th>DATA INIZIO PRESTITO</th>
                    <th>CLIENTE</th>
                    <th>BIBLIOTECARIO</th>
                    <th>TITOLO</th>
                    <th>COLLOC.</th>
                    <th>NOTE</th>
                    <th>DETTAGLIO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($prestiti as $prestito)
                    <tr class="table-primary" hoverable>
                        <td>
                            {{ $prestito->data_inizio_prestito }}

                            @if ($prestito->in_prestito)
                                <span class="badge bg-warning">
                                    {{ Carbon\Carbon::parse($prestito->data_inizio_prestito)->diffInDays(Carbon\Carbon::now(), false) }}
                                    giorni
                                </span>
                            @else
                                <span class="badge bg-success">Restituito</span>
                            @endif
                        </td>
                        <td>
                            {{ $prestito->cliente->nominativo }}
                        </td>
                        <td>
                            {{ $prestito->bibliotecario->nominativo }}
                        </td>
                        <td>
                            {{ $prestito->libro->titolo }}
                        </td>
                        <td>
                            {{ $prestito->libro->collocazione }}
                        </td>
                        <td>
                            {{ $prestito->note }}
                        </td>
                        <td>
                            <a
                                class="btn btn-warning"
                                href="{{ route("books.loans.show", ["id" => $prestito->id]) }}"
                                role="button"
                            >
                                Dettaglio prestito
                            </a>
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
