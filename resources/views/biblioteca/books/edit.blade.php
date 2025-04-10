@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Libro"])

    <form
        method="POST"
        id="form-modifica"
        action="{{ route("books.update", ["id" => $libro->id]) }}"
    >
        @csrf
        @method("PUT")
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-10">
                        <div class="">
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
                        <div class="">
                            <label for="editCollocazione">&nbsp;</label>
                            <div>
                                <a
                                    class="btn btn-success"
                                    href="{{ route("books.call-number", ["id" => $libro->id]) }}"
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
                <label for="xAutori" class="form-label">Autore/i</label>
                <livewire:search-autore
                    :persone_id="$libro->autori->pluck('id')->toArray()"
                    name_input="xIdAutori[]"
                    placeholder="Inserisci autori"
                    multiple="true"
                />
            </div>

            <div class="col-md-6">
                <label for="xEditori" class="form-label">Editore/i</label>
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
                <div class="">
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
                <div class="">
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
                <div class="">
                    <label for="categoria">Categoria</label>
                    <select class="form-select" name="categoria" type="text">
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
                <div class="">
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
                <div class="">
                    <label for="critica">Critica</label>
                    <select class="form-select" name="critica" type="text">
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
                    class="form-select"
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
                <label for="xNote" class="form-label">Note</label>
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
                href="{{ route("books.destory.create", $libro->id) }}"
            >
                Elimina
            </a>
        </div>
    </div>
@endsection
