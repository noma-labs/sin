@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Gruppo familiare"])

    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <div class="card border-dark my-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-sm-6 font-weight-bold">
                                    Gruppo Familiare:
                                </label>
                                <div class="col-sm-6">
                                    {{ $gruppo->nome }}
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-6 font-weight-bold">
                                    Capogruppo:
                                </label>
                                <div class="col-sm-6">
                                    @if ($gruppo->capogruppoAttuale())
                                        <a
                                            href="{{ route("nomadelfia.persone.dettaglio", ["idPersona" => $gruppo->capogruppoAttuale()->id]) }}"
                                        >
                                            {{ $gruppo->capogruppoAttuale()->nominativo }}
                                        </a>
                                    @else
                                        <span class="text-danger">
                                            Capogruppo non assegnato
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--end col dati gruppo -->
                        <div class="col-md-8">
                            <x-modal
                                modal-title="Modifica Capogruppo"
                                button-title="Cambia Capogruppo"
                                button-style="btn-danger my-2"
                            >
                                <x-slot:body>
                                    <form
                                        class="form"
                                        method="POST"
                                        id="formPersonaGruppoModifica{{ $gruppo->id }}"
                                        action="{{ route("nomadelfia.gruppifamiliari.capogruppo", ["id" => $gruppo->id]) }}"
                                    >
                                        @csrf
                                        <div class="row">
                                            <label
                                                for="staticEmail"
                                                class="col-sm-6 col-form-label"
                                            >
                                                Capogruppo Attuale
                                            </label>
                                            <div class="col-sm-6">
                                                @if ($gruppo->capogruppoAttuale())
                                                    <div>
                                                        {{ $gruppo->capogruppoAttuale()->nominativo }}
                                                    </div>
                                                @else
                                                    <span class="text-danger">
                                                        Capogruppo non assegnato
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label
                                                for="staticEmail"
                                                class="col-sm-6 col-form-label"
                                            >
                                                Nuovo capogruppo
                                            </label>
                                            <div class="col-sm-6">
                                                <select
                                                    class="form-control"
                                                    name="nuovo"
                                                >
                                                    <option value="" selected>
                                                        ---scegli capogruppo--
                                                    </option>
                                                    @foreach ($gruppo->capogruppiPossibili() as $p)
                                                        <option
                                                            value="{{ $p->id }}"
                                                        >
                                                            {{ $p->nominativo }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label
                                                class="col-sm-6 col-form-label"
                                            >
                                                Data Cambio
                                            </label>
                                            <div class="col-sm-6">
                                                <input
                                                    class="form-control"
                                                    type="date"
                                                    name="inizio"
                                                />
                                            </div>
                                        </div>
                                    </form>
                                </x-slot>
                                <x-slot:footer>
                                    <button
                                        class="btn btn-success"
                                        form="formPersonaGruppoModifica{{ $gruppo->id }}"
                                    >
                                        Salva
                                    </button>
                                </x-slot>
                            </x-modal>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-md-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Persone divise per famiglie</h4>
                    <ul class="list-group list-group-flush">
                        @foreach ($single as $persona)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="font-weight-bold mt-2">
                                            @include("nomadelfia.templates.persona", ["persona" => $persona])
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-2"></div>
                                </div>
                            </li>
                        @endforeach

                        @foreach ($famiglie as $famiglia_id => $componenti)
                            <li class="list-group-item">
                                <div class="row">
                                    @if ($famiglia_id === "")
                                        <p class="text-danger">
                                            Persone senza famiglia
                                        </p>
                                    @else
                                        <div class="col-md-6">
                                            <div class="font-weight-bold mt-2">
                                                Fam.
                                                <a
                                                    href="{{ route("nomadelfia.famiglia.dettaglio", ["id" => $famiglia_id]) }}"
                                                >
                                                    {{ Domain\Nomadelfia\Famiglia\Models\Famiglia::find($famiglia_id)->nome_famiglia }}
                                                </a>
                                                ({{ count($componenti) }})
                                            </div>
                                            <ul>
                                                @foreach ($componenti as $componente)
                                                    <li>
                                                        @year($componente->data_nascita)
                                                        <a
                                                            href="{{ route("nomadelfia.persone.dettaglio", ["idPersona" => $componente->persona_id]) }}"
                                                        >
                                                            {{ $componente->nominativo }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-4 offset-md-2">
                                            @include("nomadelfia.templates.spostaFamigliaNuovoGruppo", ["famiglia_id" => $famiglia_id, "componenti" => $componenti, "gruppo_id" => $gruppo->id])
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        Persone
                        <span class="badge bg-primary">
                            {{ $gruppo->personeAttuale->count() }}
                        </span>
                    </h4>
                    <ul class="list-group list-group-flush">
                        @foreach ($gruppo->personeAttuale as $persona)
                            <li class="list-group-item">
                                @year($persona->data_nascita)
                                @include("nomadelfia.templates.persona", ["persona" => $persona])
                                (
                                @diffYears($persona->data_nascita)
                                anni)
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
