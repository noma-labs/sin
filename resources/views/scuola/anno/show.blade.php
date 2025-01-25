@extends("scuola.index")

@section("title", "Gestione Scuola")

@section("content")
    @include("partials.header", ["title" => "Anno scolastico " . $anno->scolastico])

    <div class="card mb-3">
        <div class="card-header">
            Scuola A.S. {{ $anno->scolastico }}
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <p>Responsabile Scuola</p>
                    @if ($resp)
                        @include("nomadelfia.templates.persona", ["persona" => $resp])
                    @else
                            Non Assegnato
                    @endif
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <p>Note</p>
                    <div>
                        @include("scuola.templates.aggiungiNoteAnno", ["anno" => $anno])
                        {{ $anno->descrizione }}
                    </div>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <p>Data inizio</p>
                    <span class="badge bg-secondary">
                        {{ $anno->data_inizio }}
                    </span>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    Studenti
                    <span class="badge bg-primary rounded-pill">
                        {{ $alunni }}
                    </span>
                </li>
                <li class="list-group-item">
                    <ul class="list-group list-group-flush">
                        @foreach ($cicloAlunni as $cicloAlunno)
                            <li
                                class="list-group-item d-flex justify-content-end align-items-center"
                            >
                                {{ $cicloAlunno->ciclo }}
                                <span
                                    class="badge bg-secondary rounded-pill ms-2"
                                >
                                    {{ $cicloAlunno->alunni_count }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>

        <div class="card-header">
            @include("scuola.templates.aggiungiClasse", ["anno" => $anno])

            @include("scuola.templates.cloneAnnoDaPrecedente", ["anno" => $anno])

        </div>
    </div>

        <div class="row  row-cols-1 row-cols-md-3 g-3">
            @foreach ($classi as $classe)
                <div class="col">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $classe->id }}"
                            >
                                <h5 class="mb-0">
                                    <button
                                        class="btn btn-link"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $classe->id }}"
                                        aria-expanded="true"
                                        aria-controls="collapse{{ $classe->id }}"
                                    >
                                        {{ $classe->tipo->nome }}
                                        <span
                                            class="badge bg-primary rounded-pill"
                                        >
                                            {{ $classe->alunni()->count() }}
                                        </span>
                                    </button>
                                </h5>
                            </div>
                            <div
                                id="collapse{{ $classe->id }}"
                                class="collapse"
                                aria-labelledby="heading{{ $classe->id }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <ul>
                                        @forelse ($classe->alunni as $alunno)
                                            <li>
                                                @year($alunno->data_nascita)
                                                @include("nomadelfia.templates.persona", ["persona" => $alunno])
                                                @liveRome($alunno)
                                                    <span
                                                        class="badge bg-warning"
                                                    >
                                                        Roma
                                                    </span>
                                                @endliveRome
                                            </li>
                                        @empty
                                            <p class="text-danger">
                                                No Studenti
                                            </p>
                                        @endforelse
                                    </ul>
                                    <a
                                        class="btn btn-primary"
                                        href="{{ route("scuola.classi.show", $classe->id) }}"
                                    >
                                        Dettaglio
                                    </a>
                                    <a
                                        class="btn btn-warning"
                                        href="{{ route("scuola.classi.elaborato.create", $classe->id) }}"
                                    >
                                        Aggiungi Elaborato
                                    </a>
                                    <x-modal
                                        modal-title="Elimina classe"
                                        button-title="Elimina"
                                        button-style="btn-danger my-2"
                                    >
                                        <x-slot:body>
                                            <form
                                                class="form"
                                                method="POST"
                                                id="formEliminaClasse{{ $classe->id }}"
                                                action="{{ route("scuola.classi.rimuovi", $classe->id) }}"
                                            >
                                                @csrf
                                                @method("delete")
                                                <body>
                                                    Vuoi davvero eliminare la
                                                    classe con tutti gli alunni
                                                    ?
                                                </body>
                                            </form>
                                        </x-slot>
                                        <x-slot:footer>
                                            <button
                                                class="btn btn-danger"
                                                form="formEliminaClasse{{ $classe->id }}"
                                            >
                                                Elimina
                                            </button>
                                        </x-slot>
                                    </x-modal>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
@endsection
