@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi elaborato"])

    <form
        method="POST"
        action="{{ route("scuola.elaborati.store") }}"
        enctype="multipart/form-data"
    >
        @csrf
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-2">
                        <label for="anno_scolastico" class="control-label">
                            Anno Scolastico
                        </label>
                        <input
                            class="form-control"
                            type="text"
                            id="anno_scolastico"
                            name="anno_scolastico"
                            value="{{ old("anno_scolastico") ? old("anno_scolastico") : $annoScolastico }}"
                        />
                    </div>
                    <div class="col-md-8">
                        <label for="titolo" class="control-label">Titolo</label>
                        <input
                            class="form-control"
                            type="text"
                            id="titolo"
                            name="titolo"
                            value="{{ old("titolo") }}"
                        />
                    </div>
                    <div class="col-md-2">
                        <label for="classe">Classe</label>
                        <div class="dropdown">
                            <button
                                class="btn btn-secondary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                Seleziona Classi
                            </button>
                            <div
                                class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton"
                            >
                                @foreach ($classi as $key => $value)
                                    <a class="dropdown-item" href="#">
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="classi[]"
                                                value="{{ $value }}"
                                            />
                                            {{ $value }}
                                        </label>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="rilegatura" class="control-label">
                            Rilegatura
                        </label>
                        <select
                            id="rilegatura"
                            name="rilegatura"
                            class="form-control"
                        >
                            @foreach ($rilegature as $option)
                                <option
                                    value="{{ $option }}"
                                    {{ old("rilegatura") ? old("rilegatura") : "" }}
                                >
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="dimensione" class="control-label">
                            Dimensioni (cm. larghezza x altezza)
                        </label>
                        <input
                            class="form-control"
                            type="text"
                            id="dimensione"
                            name="dimensione"
                            value="{{ old("dimensione") ? old("dimensione") : "" }}"
                        />
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="alunni" class="control-label">
                            Alunni/o
                        </label>
                        <livewire:search-persona
                            name_input="studenti_ids[]"
                            :multiple="true"
                        />
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="alunni" class="control-label">
                            Coordinatori/o
                        </label>
                        <livewire:search-persona
                            name_input="coordinatori_ids[]"
                            :multiple="true"
                        />
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="note" class="control-label">Note</label>
                        <textarea
                            class="form-control"
                            id="note"
                            name="note"
                            rows="5"
                            placeholder="-- Inserisci il sommario del libro--- "
                        >
                        {{ old("note") }}
                        </textarea>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="file" class="form-label">Scegli file</label>
                        <input type="file" id="file" name="file" />
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="submit">
                            Salva
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
