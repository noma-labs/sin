@extends("biblioteca.books.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi Libro"])

    <form method="POST" action="{{ route("books.store") }}">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label">
                                    Collocazione -lettere
                                </label>
                                <livewire:search-collocazione-lettere />
                            </div>
                            <div class="col-md-4">
                                <livewire:search-collocazione-numeri
                                    :show-free="true"
                                    :show-busy="false"
                                    :show-next="true"
                                    name="xCollocazione"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="xTitolo" class="control-label">
                            Titolo (*)
                        </label>
                        <input
                            class="form-control"
                            name="xTitolo"
                            type="text"
                            value="{{ old("xTitolo") }}"
                            placeholder="Inserisci titolo libro..."
                            autocomplete="off"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="xEditori" class="control-label">
                                    Autore/i
                                </label>
                                <livewire:search-autore
                                    id="xEditori"
                                    name_input="xIdAutori[]"
                                    placeholder="Inserisci autori"
                                    multiple="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <a
                                    class="btn btn-success"
                                    href="{{ route("autori.create") }}"
                                    target="_blank"
                                    role="button"
                                >
                                    Nuovo Autore
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="xEditori" class="control-label">
                                    Editore/i
                                </label>
                                <livewire:search-editore
                                    id="xEditori"
                                    name_input="xIdEditori[]"
                                    placeholder="Inserisci editori"
                                    multiple="true"
                                />
                            </div>
                            <div class="col-md-4">
                                <a
                                    class="btn btn-success"
                                    href="{{ route("editori.create") }}"
                                    target="_blank"
                                    role="button"
                                >
                                    Nuovo Editore
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="xClassificazione" class="control-label">
                            Classificazione (*)
                        </label>
                        <select
                            class="form-select"
                            name="xClassificazione"
                            type="text"
                            id="xClassificazione"
                        >
                            <option disabled selected>
                                ---Seleziona la Classificazione---
                            </option>
                            @foreach ($classificazioni as $cls)
                                @if (old("xClassificazione") != null)
                                    @if (old("xClassificazione") == $cls->id)
                                        <option
                                            value="{{ $cls->id }}"
                                            selected
                                        >
                                            {{ $cls->descrizione }}
                                        </option>
                                    @else
                                        <option value="{{ $cls->id }}">
                                            {{ $cls->descrizione }}
                                        </option>
                                    @endif
                                @else
                                    <option value="{{ $cls->id }}">
                                        {{ $cls->descrizione }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="dimensione">Dimensione</label>
                        <input
                            class="form-control"
                            type="text"
                            name="dimensione"
                            value="{{ old("dimensione") }}"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="critica">Critica</label>
                        <select class="form-select" name="critica" type="text">
                            <option disabled selected>
                                ---Seleziona la critica---
                            </option>
                            @foreach (App\Biblioteca\Models\Libro::getEnum("critica") as $crit)
                                @if (old("critica") == $crit)
                                    <option value="{{ $crit }}" selected>
                                        {{ $crit }}
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
                <div class="row">
                    <div class="col-md-4">
                        <label for="isbn">ISBN</label>
                        <input
                            class="form-control"
                            type="text"
                            autocomplete="off"
                            value="{{ old("isbn") }}"
                            name="isbn"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="data_pubblicazione">
                            Data pubblicazione
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="dataPubblicazione"
                            value="{{ old("data_pubblicazione") }}"
                            name="data_pubblicazione"
                        />
                    </div>
                    <div class="col-md-4">
                        <label for="categoria">Categoria</label>
                        <select
                            class="form-select"
                            name="categoria"
                            type="text"
                        >
                            <option disabled selected>
                                ---Seleziona la categoria---
                            </option>
                            @foreach (App\Biblioteca\Models\Libro::getEnum("categoria") as $cat)
                                @if (old("categoria") == $cat)
                                    <option value="{{ $cat }}" selected>
                                        {{ $cat }}
                                    </option>
                                @else
                                    <option value="{{ $cat }}">
                                        {{ $cat }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="xNote" class="control-label">Note</label>
                        <textarea
                            class="form-control"
                            name="xNote"
                            class="text"
                            rows="2"
                        >
{{ old("xNote") }}</textarea
                        >
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="stampaEtichetta"
                                id="addToEtichette"
                                value="aggiungiEtichetta"
                                checked
                            />
                            <label
                                class="form-check-label"
                                for="addToEtichette"
                            >
                                Aggiungi il nuovo libro nella lista delle
                                etichette da stampare.
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                name="stampaEtichetta"
                                id="notPrint"
                                value="noEtichetta"
                            />
                            <label class="form-check-label" for="notPrint">
                                Non aggiungere il libro nella stampa delle
                                etichette.
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <p class="text-end text-danger">
                            Le informazioni segnate con (*) sono obbligatorie.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <button
                    class="btn btn-success"
                    name="_addanother"
                    value="true"
                    type="submit"
                >
                    Salva e aggiungi un'altro Libro
                </button>
                <button class="btn btn-success" value="true" type="submit">
                    Salva
                </button>
            </div>
        </div>
    </form>
@endsection
