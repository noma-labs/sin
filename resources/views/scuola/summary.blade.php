@extends("scuola.index")

@section("title", "Gestione Scuola")

@section("content")
    <div class="card mt-3">
        <div class="card-header">A.S. {{ $lastAnno->scolastico }}</div>
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
                    Studenti
                    <span class="badge bg-primary rounded-pill">
                        {{ $alunni }}
                    </span>
                </li>
                @foreach ($cicloAlunni as $c)
                    <li
                        class="list-group-item d-flex justify-content-end align-items-center"
                    >
                        <p class="m-2">{{ ucfirst($c->ciclo) }}</p>
                        <span class="badge bg-primary rounded-pill">
                            {{ $c->alunni_count }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="card-footer">
            <div class="d-grid gap-2 d-md-block d-md-flex justify-content-end">
                <a
                    class="btn btn-primary my-2"
                    role="button"
                    href="{{ route("scuola.anno.show", $lastAnno->id) }}"
                >
                    Dettaglio
                </a>
                <x-modal
                    modal-title="Esporta Elenchi"
                    button-title="Esporta Elenchi"
                    button-style="btn-secondary my-2"
                >
                    <x-slot:body>
                        <form
                            class="form"
                            method="POST"
                            id="formStampa"
                            action="{{ route("scuola.stampa") }}"
                        >
                            @csrf
                            <p>Seleziona gli elenchi da stampare:</p>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="studenti"
                                    id="defaultCheck1"
                                    name="elenchi[]"
                                    checked
                                />
                                <label
                                    class="form-check-label"
                                    for="defaultCheck1"
                                >
                                    Elenco Studenti
                                </label>
                            </div>
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    value="coordinatori"
                                    id="defaultCheck1"
                                    name="elenchi[]"
                                    checked
                                />
                                <label
                                    class="form-check-label"
                                    for="defaultCheck1"
                                >
                                    Elenco Coordinatori
                                </label>
                            </div>
                        </form>
                    </x-slot>
                    <x-slot:footer>
                        <button class="btn btn-success" form="formStampa">
                            Esporta (.doc)
                        </button>
                    </x-slot>
                </x-modal>
                @include("scuola.templates.cloneAnnoDaPrecedente", ["anno" => $lastAnno])
            </div>
        </div>
    </div>
@endsection
