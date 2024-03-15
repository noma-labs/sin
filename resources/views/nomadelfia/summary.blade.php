@extends("nomadelfia.index")

@section("title", "Gestione Nomadelfia")

@section("archivio")
    <livewire:search-persona />
    <div class="card-deck">
        <div class="card">
            <div class="card-header">Gestione Popolazione</div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a href="{{ route("nomadelfia.popolazione") }}">
                            Popolazione Nomadelfia
                        </a>
                        <span class="badge badge-primary badge-pill">
                            {{ $totale }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.maggiorenni") }}"
                        >
                            Maggiorenni
                        </a>
                        <p>Donne ({{ count($maggiorenni->donne) }})</p>
                        <p>Uomini ({{ count($maggiorenni->uomini) }})</p>
                        <span class="badge badge-primary badge-pill">
                            {{ $maggiorenni->total }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.posizione.effettivi") }}"
                        >
                            Effettivi
                        </a>
                        <p>Donne ({{ count($effettivi->donne) }})</p>
                        <p>Uomini ({{ count($effettivi->uomini) }})</p>
                        <span class="badge badge-primary badge-pill">
                            {{ $effettivi->total }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.posizione.postulanti") }}"
                        >
                            Postulanti
                        </a>
                        <p>Donne ({{ count($postulanti->donne) }})</p>
                        <p>Uomini ({{ count($postulanti->uomini) }})</p>
                        <span class="badge badge-primary badge-pill">
                            {{ $postulanti->total }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.posizione.ospiti") }}"
                        >
                            Ospiti
                        </a>
                        <p>Donne ({{ count($ospiti->donne) }})</p>
                        <p>Uomini ({{ count($ospiti->uomini) }})</p>
                        <span class="badge badge-primary badge-pill">
                            {{ $ospiti->total }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.stati.sacerdoti") }}"
                        >
                            Sacerdoti
                        </a>
                        <span class="badge badge-primary badge-pill">
                            {{ count($sacerdoti) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.stati.mammevocazione") }}"
                        >
                            Mamme di Vocazione
                        </a>
                        <span class="badge badge-primary badge-pill">
                            {{ count($mvocazione) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.stati.nomadelfamamma") }}"
                        >
                            Nomadelfa Mamma
                        </a>
                        <span class="badge badge-primary badge-pill">
                            {{ count($nomanamma) }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.posizione.figli.maggiorenni") }}"
                        >
                            Figli Maggiorenni
                        </a>
                        <p>Donne ({{ count($figliMaggiorenni->donne) }})</p>
                        <p>Uomini ({{ count($figliMaggiorenni->uomini) }})</p>
                        <span class="badge badge-primary badge-pill">
                            {{ $figliMaggiorenni->total }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <a
                            href="{{ route("nomadelfia.popolazione.posizione.figli.minorenni") }}"
                        >
                            Figli Minorenni
                        </a>
                        <p>Donne ({{ count($minorenni->donne) }})</p>
                        <p>Uomini ({{ count($minorenni->uomini) }})</p>
                        <span class="badge badge-primary badge-pill">
                            {{ $minorenni->total }}
                        </span>
                    </li>
                </ul>
            </div>
            <div class="card-footer">
                <a
                    href="{{ route("nomadelfia.popolazione") }}"
                    class="text-center btn btn-primary"
                >
                    Entra
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Gestione Famiglie</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Famiglie per posizione</label>
                        <ul>
                            @foreach ($posizioniFamiglia as $posizione)
                                <li>
                                    {{ $posizione->posizione_famiglia }} :
                                    <strong>{{ $posizione->count }}</strong>

                                    @if ($posizione->sesso == "F")
                                        <span class="badge badge-primary">
                                            {{ $posizione->sesso }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            {{ $posizione->sesso }}
                                        </span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-md-6">
                        <label>Famiglie Numerose</label>
                        <ul>
                            @foreach ($famiglieNumerose as $fam)
                                <li>
                                    <a
                                        href="{{ route("nomadelfia.famiglia.dettaglio", ["id" => $fam->id]) }}"
                                    >
                                        {{ $fam->nome_famiglia }}
                                    </a>
                                    <span class="badge badge-primary">
                                        {{ $fam->componenti }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a
                    href="{{ route("nomadelfia.famiglie") }}"
                    class="btn btn-primary"
                >
                    Entra
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Gestione Gruppi Familiari</div>
            <div class="card-body">
                <ul>
                    @foreach ($gruppi as $gruppo)
                        <li>
                            @include("nomadelfia.templates.gruppo", ["id" => $gruppo->id, "nome" => $gruppo->nome])
                            <strong>
                                <span class="badge badge-primary badge-pill">
                                    {{ $gruppo->count }}
                                </span>
                            </strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <a
                    href="{{ route("nomadelfia.gruppifamiliari") }}"
                    class="btn btn-primary"
                >
                    Entra
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Attività recenti</div>
            <div class="card-body">
                <ul>
                    @forelse ($activities as $act)
                        <li>
                            @if ($act->isEnterEvent())
                                <span class="badge badge-success">Entrata</span>
                            @endif

                            @if ($act->isExitEvent())
                                <span class="badge badge-danger">Uscita</span>
                            @endif

                            @if ($act->isDeathEvent())
                                <span class="badge badge-dark">Decesso</span>
                            @endif

                            @include("nomadelfia.templates.persona", ["persona" => $act->subject])
                        </li>
                    @empty
                        <p class="font-italic">Non ci sono attività recenti</p>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer">
                <a
                    href="{{ route("nomadelfia.activity") }}"
                    class="btn btn-primary"
                >
                    Entra
                </a>
            </div>
        </div>
    </div>
    <!-- end card deck-->

    <div class="row justify-content-md-center my-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h4>Statistiche</h4>
                    <ul class="list-group list-group-flush">
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center"
                        >
                            <p>Eta massima</p>
                            <span class="badge badge-primary badge-pill">
                                {{ $stats->max }}
                            </span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center"
                        >
                            <p>Eta media</p>
                            <span class="badge badge-primary badge-pill">
                                {{ $stats->avg }}
                            </span>
                        </li>
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center"
                        >
                            <p>Eta minima</p>
                            <span class="badge badge-primary badge-pill">
                                {{ $stats->min }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <my-modal
        modal-title="Stampa elenchi"
        button-title="Stampa Popolazione Nomadelfia"
        button-style="btn-success my-2"
    >
        <template slot="modal-body-slot">
            <form
                class="form"
                method="POST"
                id="formStampa"
                action="{{ route("nomadelfia.popolazione.stampa") }}"
            >
                {{ csrf_field() }}
                <h5>Seleziona gli elenchi da stampare:</h5>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="maggMin"
                        id="defaultCheck1"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck1">
                        Popolazione Maggiorenni, Minorenni
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="effePostOspFig"
                        id="defaultCheck1"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck1">
                        Effettivi, Postulanti, Ospiti, Figli
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="famiglie"
                        id="defaultCheck2"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck2">
                        Famiglie
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="gruppi"
                        id="defaultCheck2"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck2">
                        Gruppi familiari
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="aziende"
                        id="defaultCheck2"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck2">
                        Aziende
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="incarichi"
                        id="defaultCheck2"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck2">
                        Incarichi
                    </label>
                </div>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="scuola"
                        id="defaultCheck2"
                        name="elenchi[]"
                        checked
                    />
                    <label class="form-check-label" for="defaultCheck2">
                        Scuola
                    </label>
                </div>
            </form>
        </template>
        <template slot="modal-button">
            <button class="btn btn-success" form="formStampa">Salva</button>
        </template>
    </my-modal>
@endsection
