@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Modifica elaborato"])
    <div class="row">
        <div class="col-md-8">
            <form
                method="POST"
                action="{{ route("scuola.elaborati.update", $elaborato->id) }}"
            >
                @csrf
                @method("PUT")
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-2">
                                <label
                                    for="anno_scolastico"
                                    class="control-label"
                                >
                                    Anno/scol
                                </label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="anno_scolastico"
                                    name="anno_scolastico"
                                    value="{{ old("anno_scolastico") ? old("anno_scolastico") : $elaborato->anno_scolastico }}"
                                />
                            </div>
                            <div class="col-md-10">
                                <label for="titolo" class="control-label">
                                    Titolo
                                </label>
                                <input
                                    class="form-control"
                                    type="text"
                                    id="titolo"
                                    name="titolo"
                                    value="{{ old("titolo") ? old("titolo") : $elaborato->titolo }}"
                                />
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-2">
                                <label for="titolo" class="control-label">
                                    Dimensioni (cm)
                                </label>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">larghezza</div>
                                    </div>
                                    <input type="number"  placeholder="--larghezza in cm--" id="larghezza" name="dimesione_larghezza" class="form-control" value="{{ old("dimesione_larghezza") ? old("dimesione_larghezza") : $elaborato->dimensione }}" >
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                      <div class="input-group-text">altezza</div>
                                    </div>
                                    <input type="number" placeholder="-- altezza in cm --"  id="altezza" name="dimensione_altezza" class="form-control">
                                </div>
                            </div>
                          </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label for="note" class="control-label">
                                    Note
                                </label>
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
        </div>
    </div>
@endsection
