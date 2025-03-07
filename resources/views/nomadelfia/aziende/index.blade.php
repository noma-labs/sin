@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Aziende"])
    <div class="row row-cols-1 row-cols-md-3 g-3">
        @foreach ($aziende as $azienda)
            <div class="col">
                <div id="accordion">
                    <div class="card">
                        <div
                            class="card-header"
                            id="heading{{ $azienda->id }}"
                        >
                            <h5 class="mb-0">
                                <button
                                    class="btn btn-link"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $azienda->id }}"
                                    aria-expanded="true"
                                    aria-controls="collapse{{ $azienda->id }}"
                                >
                                    {{ $azienda->nome_azienda }}
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $azienda->lavoratoriAttuali->count() }}
                                    </span>
                                </button>
                            </h5>
                        </div>

                        <div
                            id="collapse{{ $azienda->id }}"
                            class="collapse"
                            aria-labelledby="heading{{ $azienda->id }}"
                            data-parent="#accordion"
                        >
                            <div class="card-body">
                                <ul>
                                    @foreach ($azienda->lavoratoriAttuali as $lavoratore)
                                        <li>
                                            @include("nomadelfia.templates.persona", ["persona" => $lavoratore])
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="row">
                                    <a
                                        class="btn btn-danger col-md-4 offset-md-2"
                                        type="button"
                                        href="{{ route("nomadelfia.aziende.edit", $azienda->id) }}"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
