@include("partials.header", ["title" => "Ricerca Libro", "subtitle" => App\Biblioteca\Models\Libro::count() . " libri"])
<form method="GET" class="form" action="{{ route("books.search") }}">
    @csrf
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
                <div class="row">
                    <div class="col-md-8">
                        <label class="form-label">Collocazione -lettere</label>
                        <livewire:search-collocazione-lettere />
                    </div>
                    <div class="col-md-4">
                        <livewire:search-collocazione-numeri
                            :show-free="false"
                            :show-next="false"
                            name="xCollocazione"
                        />
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="">
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
    <div class="row g-3">
        <div class="col-md-6">
            <label for="xAutore" class="control-label">
                Autore ({{ App\Biblioteca\Models\Autore::count() }})
            </label>
            <livewire:search-autore name_input="xIdAutore" />
        </div>
        <div class="col-md-6">
            <label for="xEditore" class="control-label">
                Editore ({{ App\Biblioteca\Models\Editore::count() }})
            </label>

            <livewire:search-editore name_input="xIdEditore" />
        </div>
        <div class="col-md-4">
            <label for="xClassificazione" class="control-label">
                Classificazione
                ({{ App\Biblioteca\Models\Classificazione::count() }})
            </label>
            <select
                class="form-select"
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
        <div class="col-md-4">
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
        <div class="col-md-2">
            <label for="categoria">Categoria</label>
            <select class="form-select" name="xCategoria" id="categoria">
                <option value="" hidden>Seleziona...</option>
                <option value="piccoli">PICCOLI</option>
                <option value="elementari">ELEMENTARI</option>
                <option value="medie">MEDIE</option>
                <option value="superiori">SUPERIORI</option>
                <option value="adulti">ADULTI</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button
                class="btn btn-success"
                id="biblio"
                name="biblioteca"
                type="submit"
            >
                Cerca Libri
            </button>
        </div>
    </div>
</form>
