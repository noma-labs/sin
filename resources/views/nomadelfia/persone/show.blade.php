@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => $persona->nome . " " . $persona->cognome])

    <div class="row mb-3 justify-content-md-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if ($persona->isPersonaInterna())
                        <h2>
                            Stato Attuale:
                            <span class="badge bg-success">Presente</span>
                        </h2>
                        <p>
                            Entrata in data
                            <strong>
                                {{ $persona->getDataEntrataNomadelfia()->toDateString() }}
                            </strong>
                            <span class="badge text-bg-info">
                                @diffHumans($persona->getDataEntrataNomadelfia())
                            </span>
                        </p>

                        <x-modal
                            button-title="Uscita"
                            button-style="btn btn-danger my-2"
                            modal-title="Uscita dalla comunità"
                        >
                            <x-slot:body>
                                <form
                                    class="form"
                                    method="POST"
                                    id="formUscitaPersona{{ $persona->id }}"
                                    action="{{ route("nomadelfia.leave.store", $persona->id) }}"
                                >
                                    @csrf
                                    <p>
                                        Inserire la data di uscita di
                                        {{ $persona->nominativo }}
                                    </p>
                                    <input
                                        class="form-control"
                                        type="date"
                                        name="data_uscita"
                                    />
                                </form>
                            </x-slot>
                            <x-slot:footer>
                                <button
                                    class="btn btn-success"
                                    form="formUscitaPersona{{ $persona->id }}"
                                >
                                    Salva
                                </button>
                            </x-slot>
                        </x-modal>
                    @else
                        <h2>
                            Stato Attuale:
                            <span class="badge bg-secondary">Uscito</span>
                        </h2>
                        <p>
                            Uscita in data
                            {{ $persona->getDataUscitaNomadelfia() ? $persona->getDataUscitaNomadelfia()->toDateString() : "" }}
                            <span class="badge text-bg-info">
                                @diffHumans($persona->getDataUscitaNomadelfia())
                            </span>
                        </p>

                        <x-modal
                            modal-title="Entrata nella comunità"
                            button-title="Entrata"
                            button-style="btn-success my-2"
                        >
                            <x-slot:body>
                                @include("nomadelfia.templates.entrataPersona", ["persona" => $persona])
                            </x-slot>
                        </x-modal>
                    @endif

                    <a
                        class="btn btn-warning"
                        href="{{ route("nomadelfia.persone.popolazione", ["idPersona" => $persona->id]) }}"
                    >
                        Vedi Storico
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-header">Dati anagrafici</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Nome:</label>
                                <div class="col-sm-8">
                                    <span>{{ $persona->nome }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Cognome:</label>
                                <div class="col-sm-8">
                                    <span>{{ $persona->cognome }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Codice Fiscale:
                                </label>
                                <div class="col-sm-8">
                                    <span>
                                        @if ($persona->cf)
                                            {{ $persona->cf }}
                                        @else
                                            <p class="text-danger">
                                                Nessun codice fiscale
                                            </p>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Data Nascita:
                                </label>
                                <div class="col-sm-8">
                                    <span>
                                        {{ $persona->data_nascita }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Luogo Nascita:
                                </label>
                                <div class="col-sm-8">
                                    <span>
                                        {{ $persona->provincia_nascita }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Sesso:</label>
                                <div class="col-sm-8">
                                    <span>{{ $persona->sesso }}</span>
                                </div>
                            </div>
                        </li>
                        @if ($persona->isDeceduta())
                            <li class="list-group-item">
                                <div class="row">
                                    <label class="col-sm-4 fw-bold">
                                        Data decesso:
                                    </label>
                                    <div class="col-sm-8">
                                        <span>
                                            {{ $persona->data_decesso }}
                                        </span>
                                    </div>
                                </div>
                            </li>
                        @endif

                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Biografia:
                                </label>
                                <div class="col-sm-8">
                                    @if ($persona->biografia)
                                        <textarea
                                            class="form-control"
                                            id="biografia"
                                            readonly
                                        >
 {{ $persona->biografia }}</textarea
                                        >
                                    @else
                                        <p class="text-danger">
                                            Nessuna biografia
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    </ul>
                    <a
                        class="btn btn-warning my-2"
                        href="{{ route("nomadelfia.person.identity.edit", $persona->id) }}"
                        role="button"
                    >
                        Modifica
                    </a>
                    @if (! $persona->isDeceduta())
                        <x-modal
                            modal-title="Decesso di persona"
                            button-title="Decesso"
                            button-style="btn-danger my-2"
                        >
                            <x-slot:body>
                                <form
                                    class="form"
                                    method="POST"
                                    id="formDecessoPersona{{ $persona->id }}"
                                    action="{{ route("nomadelfia.persone.decesso", ["idPersona" => $persona->id]) }}"
                                >
                                    @csrf
                                    <p>
                                        Inserire la data di decesso di
                                        {{ $persona->nominativo }}
                                    </p>
                                    <input
                                        class="form-control"
                                        type="date"
                                        name="data_decesso"
                                    />
                                </form>
                            </x-slot>
                            <x-slot:footer>
                                <button
                                    class="btn btn-danger"
                                    form="formDecessoPersona{{ $persona->id }}"
                                >
                                    Salva
                                </button>
                            </x-slot>
                        </x-modal>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-3">
            <div class="card">
                <div class="card-header">Dati Nomadelfia</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Origine:</label>
                                <div class="col-sm-6">
                                    @if ($persona->origine)
                                        <span>
                                            {{ $persona->origine }}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            Nessuna origine
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Nominativo:
                                </label>
                                <div class="col-sm-6">
                                    {{ $persona->nominativo }}
                                </div>
                                <div class="col-sm-2">
                                    <a
                                        class="btn btn-warning"
                                        href="{{ route("nomadelfia.persone.nominativo.modifica.view", $persona->id) }}"
                                        role="button"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Numero Elenco:
                                </label>
                                <div class="col-sm-6">
                                    @if ($persona->numero_elenco)
                                        <span class="badge text-bg-info">
                                            {{ $persona->numero_elenco }}
                                        </span>
                                    @else
                                        <p class="text-danger">
                                            Nessun numero elenco
                                        </p>
                                    @endif
                                </div>
                                <div class="col-sm-2">
                                    @if ($persona->numero_elenco)
                                    @else
                                        <a
                                            class="btn btn-warning"
                                            href="{{ route("nomadelfia.persone.numelenco.modifica.view", $persona->id) }}"
                                            role="button"
                                        >
                                            Modifica
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Stato familiare:
                                </label>
                                <div class="col-sm-6">
                                    @if ($persona->statoAttuale() != null)
                                        <span>
                                            {{ $persona->statoAttuale()->nome }}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            Nessuno stato
                                        </span>
                                    @endif
                                </div>
                                <div class="col-sm-2">
                                    <a
                                        class="btn btn-warning"
                                        href="{{ route("nomadelfia.persone.stato", ["idPersona" => $persona->id]) }}"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Posizione:
                                </label>
                                <div class="col-sm-6">
                                    @if ($posizioneAttuale != null)
                                        {{ $posizioneAttuale->nome }}
                                    @else
                                        <span class="text-danger">
                                            Nessuna posizione
                                        </span>
                                    @endif
                                </div>
                                <div class="col-sm-2">
                                    <a
                                        class="btn btn-warning"
                                        href="{{ route("nomadelfia.persone.posizione", ["idPersona" => $persona->id]) }}"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Gruppo familiare:
                                </label>
                                <div class="col-sm-6">
                                    @if ($gruppoAttuale)
                                        <a
                                            href="{{ route("nomadelfia.gruppifamiliari.dettaglio", [$gruppoAttuale->id]) }}"
                                        >
                                            {{ $gruppoAttuale->nome }}
                                        </a>
                                    @else
                                        <span class="text-danger">
                                            Nessun gruppo
                                        </span>
                                    @endif
                                </div>
                                <div class="col-sm-2">
                                    <a
                                        class="btn btn-warning"
                                        href="{{ route("nomadelfia.persone.gruppofamiliare", ["idPersona" => $persona->id]) }}"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Azienda/e:
                                </label>
                                <div class="col-sm-6">
                                    @forelse ($persona->aziendeAttuali()->get() as $azienda)
                                        <p>
                                            <a
                                                href="{{ route("nomadelfia.aziende.edit", [$azienda->id]) }}"
                                            >
                                                {{ $azienda->nome_azienda }}
                                            </a>
                                            ({{ $azienda->pivot->mansione }})
                                        </p>
                                    @empty
                                        <span class="text-danger">
                                            Nessuna azienda
                                        </span>
                                    @endforelse
                                </div>
                                <div class="col-sm-2">
                                    <a
                                        class="btn btn-warning"
                                        href="{{ route("nomadelfia.persone.aziende", ["idPersona" => $persona->id]) }}"
                                    >
                                        Modifica
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Incarichi:
                                </label>
                                <div class="col-sm-6">
                                    @forelse ($persona->incarichiAttuali()->get() as $incarico)
                                        <p>
                                            <a
                                                href="{{ route("nomadelfia.incarichi.edit", [$incarico->id]) }}"
                                            >
                                                {{ $incarico->nome }}
                                            </a>
                                            (
                                            @diffHumans($incarico->pivot->data_inizio)
                                            )
                                        </p>
                                    @empty
                                        <span class="text-danger">
                                            Nessun incarico
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @can("scuola.visualizza")
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Dati Famiglia</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @if ($famigliaEnrico)
                                <div class="alert alert-warning" role="alert">
                                    <strong>ALFA Enrico:</strong>
                                    {{ Illuminate\Support\Str::headline($famigliaEnrico->famiglia) }}
                                </div>
                            @endif

                            @if ($famigliaAttuale)
                                <li class="list-group-item">
                                    <div class="row">
                                        <label class="col-sm-4 fw-bold">
                                            Nome Famiglia:
                                        </label>
                                        <div class="col-sm-8">
                                            <a
                                                href="{{ route("nomadelfia.famiglia.dettaglio", ["id" => $persona->famigliaAttuale()->id]) }}"
                                            >
                                                {{ $persona->famigliaAttuale()->nome_famiglia }}
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="row">
                                        <label class="col-sm-4 fw-bold">
                                            Posizione:
                                        </label>
                                        <div class="col-sm-8">
                                            <span>
                                                {{ $persona->famigliaAttuale()->pivot->posizione_famiglia }}
                                            </span>
                                        </div>
                                    </div>
                                </li>
                            @else
                                <li class="list-group-item">
                                    <div class="row">
                                        <label class="col-sm-4 fw-bold">
                                            Tipo:
                                        </label>
                                        <div class="col-sm-8">
                                            <span>Single</span>
                                        </div>
                                    </div>
                                </li>
                            @endif
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-sm-8">
                                        <a
                                            class="btn btn-warning"
                                            href="{{ route("nomadelfia.persone.famiglie", ["idPersona" => $persona->id]) }}"
                                        >
                                            Modifica
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Scuola</div>
                    <div class="card-body">
                        <a
                            href="{{ route("scuola.student.show", $persona->id) }}"
                        >
                            Alunno
                        </a>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
