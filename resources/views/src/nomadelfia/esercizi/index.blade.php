@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Esercizi Spirituali"])

    @foreach (collect($esercizi)->chunk(3) as $chunk)
        <div class="row">
            <div class="col-md-4 my-1">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="heading0">
                            <h5 class="mb-0">
                                <button
                                    class="btn btn-link"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseNo"
                                    aria-expanded="false"
                                    aria-controls="collapseNo"
                                >
                                    Persone senza Es. Spirituale
                                    <span
                                        class="badge text-bg-primary rounded-pill"
                                    >
                                        {{ $noEsercizi->total }}
                                    </span>
                                </button>
                            </h5>
                        </div>
                        <div
                            id="collapseNo"
                            class="collapse"
                            aria-labelledby="heading0"
                            data-parent="#accordion"
                        >
                            <div class="card-body">
                                <p>Persone:</p>
                                <ul>
                                    @forelse ($noEsercizi->uomini as $persona)
                                        <li>
                                            @include("nomadelfia.templates.persona", ["persona" => $persona])
                                        </li>
                                    @empty
                                        <p class="text-danger">
                                            Nessun maggiorenne
                                        </p>
                                    @endforelse
                                    @forelse ($noEsercizi->donne as $persona)
                                        <li>
                                            @include("nomadelfia.templates.persona", ["persona" => $persona])
                                        </li>
                                    @empty
                                        <p class="text-danger">
                                            Nessun maggiorenne
                                        </p>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @foreach ($chunk as $esercizio)
                <div class="col-md-4 my-1">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $esercizio->id }}"
                            >
                                <h5 class="mb-0">
                                    <button
                                        class="btn btn-link"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $esercizio->id }}"
                                        aria-expanded="false"
                                        aria-controls="collapse{{ $esercizio->id }}"
                                    >
                                        {{ $esercizio->turno }}
                                        <span
                                            class="badge text-bg-primary rounded-pill"
                                        >
                                            {{ $esercizio->personeOk()->total }}
                                        </span>
                                    </button>
                                </h5>
                            </div>
                            <div
                                id="collapse{{ $esercizio->id }}"
                                class="collapse"
                                aria-labelledby="heading{{ $esercizio->id }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <p>Responsabile:</p>

                                    @if ($esercizio->responsabile)
                                        <span class="text-bold">
                                            {{ $esercizio->responsabile->nominativo }}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            Responsabile non assegnato
                                        </span>
                                    @endif
                                    <a
                                        class="btn btn-primary"
                                        href="{{ route("nomadelfia.esercizi.dettaglio", $esercizio->id) }}"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    <x-modal
        modal-title="Esporta Elechi"
        button-title="Esporta Es.Spirituali"
        button-style="btn-success my-2"
    >
        <x-slot:body>
            <form
                class="form"
                method="get"
                id="formStampa"
                action="{{ route("nomadelfia.esercizi.stampa") }}"
            >
                <h5>Esporta esercizi Spirituali in Word (.docx)</h5>
            </form>
        </x-slot>
        <x-slot:footer>
            <button class="btn btn-success" form="formStampa">Salva</button>
        </x-slot>
    </x-modal>
@endsection
