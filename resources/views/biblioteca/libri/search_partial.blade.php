<div class="my-page-title">
    <div class="d-flex justify-content-end">
        <div class="mr-auto p-2">
            <span class="h1 text-center">Ricerca Libro</span>
        </div>
        <div class="p-2 text-right">
            <h5 class="m-1">
                {{ App\Biblioteca\Models\Libro::count() }} libri presenti nella
                biblioteca
            </h5>
            @if (Auth::check())
                <h5 class="m-1">
                    {{ App\Biblioteca\Models\Libro::onlyTrashed()->count() }}
                    libri scartati o eliminati
                </h5>
            @endif
        </div>
    </div>
</div>

<form method="GET" class="form" action="{{ route("libri.ricerca.submit") }}">
    {{ csrf_field() }}
    @if (Auth::guest())
        <div class="row">
            <div class="col-md-12">
                <label for="xTitolo" class="control-label">Titolo</label>
                <input
                    class="form-control"
                    name="xTitolo"
                    type="text"
                    placeholder="Inserisci il testo da ricercare nel titolo..."
                />
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-6">
                <search-collocazione
                    title="Collocazione ({{ App\Biblioteca\Models\ViewCollocazione::total() }}) - Lettere"
                    url-lettere="{{ route("api.biblioteca.collocazione") }}"
                    numeri-required="false"
                    numeri-mancanti="false"
                    numero-nuovo="false"
                ></search-collocazione>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="xTitolo" class="control-label">Titolo</label>
                    <input
                        class="form-control"
                        type="text"
                        name="xTitolo"
                        id="xIdTitolo"
                    />
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="xAutore" class="control-label">
                    Autore ({{ App\Biblioteca\Models\Autore::count() }})
                </label>
                <livewire:search-autore name_input="xIdAutore" />
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="xEditore" class="control-label">
                    Editore ({{ App\Biblioteca\Models\Editore::count() }})
                </label>

                <livewire:search-editore name_input="xIdEditore" />
            </div>
        </div>
    </div>
    <!-- end first row: collocazione, titolo, autore, editore -->
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <label for="xClassificazione" class="control-label">
                    Classificazione
                    ({{ App\Biblioteca\Models\Classificazione::count() }})
                </label>
                <select
                    class="form-control"
                    name="xClassificazione"
                    type="text"
                    id="xClassificazione"
                >
                    <option value="" selected disabled>
                        ---Seleziona Classificazione---
                    </option>
                    @foreach ($classificazioni as $cls)
                        <option value="{{ $cls->id }}">
                            {{ $cls->descrizione }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="xNote" class="control-label">Note</label>
                <input
                    class="form-control"
                    name="xNote"
                    type="text"
                    id="xNote"
                    size="20"
                    maxlength="100"
                    placeholder="Inserisci parola da ricercare nelle note"
                />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="categoria">Categoria</label>
                <select class="form-control" name="xCategoria" id="categoria">
                    <option value="" hidden>Seleziona...</option>
                    <option value="piccoli">PICCOLI</option>
                    <option value="elementari">ELEMENTARI</option>
                    <option value="medie">MEDIE</option>
                    <option value="superiori">SUPERIORI</option>
                    <option value="adulti">ADULTI</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 offset-md-10">
            <button
                class="btn btn-block btn-success"
                id="biblio"
                name="biblioteca"
                type="submit"
            >
                Cerca Libri
            </button>
        </div>
    </div>
    <!-- end second row: classificazioe, note -->
</form>
