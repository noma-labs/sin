@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Modifica elaborato"])
    <div class="row">
        <form
            method="POST"
            action="{{ route("scuola.elaborati.update", $elaborato->id) }}"
        >
            @csrf
            @method("PUT")
            <div class="col">
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-2">
                        <label for="call-number" class="form-label">
                            Collocazione
                        </label>
                        <input
                            class="form-control"
                            type="text"
                            id="call-number"
                            value="{{ old("collocazione") ? old("collocazione") : $elaborato->collocazione }}"
                            disabled
                        />
                    </div>
                    <div class="col-6 col-md-10">
                        <label for="title" class="form-label">Titolo</label>
                        <input
                            class="form-control"
                            type="text"
                            id="title"
                            name="titolo"
                            value="{{ old("titolo") ? old("titolo") : $elaborato->titolo }}"
                        />
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-2">
                        <label for="aa" class="form-label">A/s</label>
                        <input
                            class="form-control"
                            type="text"
                            id="aa"
                            name="anno_scolastico"
                            value="{{ old("anno_scolastico") ? old("anno_scolastico") : $elaborato->anno_scolastico }}"
                        />
                    </div>
                    <div class="col-6 col-md-8">
                        <label for="classe" class="form-label">Classe</label>
                        <div class="dropdown">
                            <button
                                class="btn btn-secondary dropdown-toggle"
                                type="button"
                                id="dropdownMenuButton"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                                Seleziona Classi
                            </button>
                            <div
                                class="dropdown-menu"
                                aria-labelledby="dropdownMenuButton"
                            >
                                @php
                                    $selectedClassi = explode(",", $elaborato->classi);
                                @endphp

                                @foreach ($classi as $key => $value)
                                    <a class="dropdown-item" href="#">
                                        <label class="form-label">
                                            <input
                                                type="checkbox"
                                                name="classi[]"
                                                value="{{ $value }}"
                                                @if(in_array($value, $selectedClassi)) checked @endif
                                            />
                                            {{ $value }}
                                        </label>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-6">
                        <label for="rilegatura" class="form-label">
                            Rilegatura
                        </label>
                        <select
                            id="rilegatura"
                            name="rilegatura"
                            class="form-select"
                        >
                            @foreach ($rilegature as $option)
                                <option
                                    value="{{ $option }}"
                                    {{ (old("rilegatura") ? old("rilegatura") : $elaborato->rilegatura) == $option ? "selected" : "" }}
                                >
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-6">
                        <label for="dimensione" class="form-label">
                            Dimensioni (A3, A4 oppure 210x297mm)
                        </label>
                        <input
                            class="form-control"
                            type="text"
                            id="dimensione"
                            name="dimensione"
                            value="{{ old("dimensione") ? old("dimensione") : $elaborato->dimensione }}"
                        />
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label for="note" class="form-label">Note</label>
                        <textarea
                            class="form-control"
                            id="note"
                            name="note"
                            rows="5"
                            placeholder="-- Inserisci il sommario del libro--- "
                        >
 {{ old("note") ? old("note") : $elaborato->note }}</textarea
                        >
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label for="alunni" class="form-label">Alunni/o</label>
                        <livewire:search-persona
                            :persone_id="$elaborato->studenti->pluck('id')->toArray()"
                            name_input="studenti_ids[]"
                            :multiple="true"
                        />
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <label for="coordinatori" class="form-label">
                            Coordinatori/o
                        </label>
                        <livewire:search-persona
                            :persone_id="$elaborato->coordinatori->pluck('id')->toArray()"
                            name_input="coordinatori_ids[]"
                            :multiple="true"
                        />
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-12">
                        <button class="btn btn-success" type="submit">
                            Salva
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
