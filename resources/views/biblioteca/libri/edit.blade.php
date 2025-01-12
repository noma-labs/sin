@extends("biblioteca.libri.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Libro"])

    <form
        method="POST"
        id="form-modifica"
        action="/biblioteca/libri/{{ $libro->id }}/modifica"
    >
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="xCollocazione">Collocazione</label>
                            <input
                                class="form-control"
                                name="xCollocazione"
                                value="{{ $libro->collocazione }}"
                                type="text"
                                id="xCollocazione"
                                placeholder="Inserisci Collocazione"
                                readonly
                            />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="editCollocazione">&nbsp;</label>
                            <div>
                                <a
                                    class="btn btn-success"
                                    href="{{ route("libro.collocazione", ["idLibro" => $libro->id]) }}"
                                    role="button"
                                >
                                    Modifica
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="xTitolo">Titolo</label>
                <input
                    class="form-control"
                    name="xTitolo"
                    type="text"
                    value="{{ $libro->titolo }}"
                    id="xTitolo"
                    placeholder="Inserisci Titolo Libro"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="xAutori" class="control-label">
                    Autore/i
                </label>
                <livewire:search-autore
                    :persone_id="$libro->autori->pluck('id')->toArray()"
                    name_input="xIdAutori[]"
                    placeholder="Inserisci autori"
                    multiple="true"
                />
            </div>

            <div class="col-md-6">
                <label for="xEditori" class="control-label">
                    Editore/i
                </label>
                <livewire:search-editore
                    :persone_id="$libro->editori->pluck('id')->toArray()"
                    name_input="xIdEditori[]"
                    placeholder="Inserisci editori"
                    multiple="true"
                />
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input
                        class="form-control"
                        type="text"
                        name="isbn"
                        maxlength="13"
                        value="{{ $libro->isbn }}"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="data_pubblicazione">Data pubblicazione</label>
                    <input
                        type="date"
                        class="form-control"
                        name="data_pubblicazione"
                        id="dataPubblicazione"
                        value="{{ $libro->data_pubblicazione }}"
                    />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <select class="form-control" name="categoria" type="text">
                        <option disabled selected>
                            ---Seleziona la categoria---
                        </option>
                        @foreach (App\Biblioteca\Models\Libro::getEnum("categoria") as $cat)
                            @if ($cat == $libro->categoria)
                                <option
                                    value="{{ $libro->categoria }}"
                                    selected
                                >
                                    {{ $libro->categoria }}
                                </option>
                            @else
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="dimensione">Dimensione</label>
                    <input
                        class="form-control"
                        type="text"
                        name="dimensione"
                        placeholder="Esempio 23x45cm"
                        value="{{ $libro->dimensione }}"
                    />
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="critica">Critica</label>
                    <select class="form-control" name="critica" type="text">
                        <option disabled selected>
                            ---Seleziona la critica---
                        </option>
                        @foreach (App\Biblioteca\Models\Libro::getEnum("critica") as $crit)
                            @if ($crit == $libro->critica)
                                <option value="{{ $libro->critica }}" selected>
                                    {{ $libro->critica }}
                                </option>
                            @else
                                <option value="{{ $crit }}">
                                    {{ $crit }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label for="xClassificazione">Classificazione</label>
                <select
                    class="form-control"
                    name="xClassificazione"
                    type="text"
                    id="xClassificazione"
                >
                    @foreach ($classificazioni as $cls)
                        @if ($libro->classificazione != null && $cls->id === $libro->classificazione->id)
                            <option value="{{ $cls->id }}" selected>
                                {{ $cls->descrizione }}
                            </option>
                        @else
                            <option value="{{ $cls->id }}">
                                {{ $cls->descrizione }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="xNote" class="control-label">Note</label>
                <input
                    type="text"
                    name="xNote"
                    class="form-control"
                    id="autore"
                    value="{{ $libro->note }}"
                />
            </div>
        </div>
    </form>

    <div class="row my-2">
        <div class="col-md-12">
            <button class="btn btn-success" form="form-modifica" type="submit">
                Salva Modifiche
            </button>
            <a
                class="btn btn-info"
                href="#"
                onclick="window.history.back(); return false;"
            >
                Annulla
            </a>
            <a
                class="btn btn-danger"
                href="{{ route("libro.elimina", ["idLibro" => $libro->id]) }}"
            >
                Elimina
            </a>
        </div>
    </div>
@endsection
