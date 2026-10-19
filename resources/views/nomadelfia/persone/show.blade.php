@extends("nomadelfia.index")

@section("content")
  @include("partials.header", ["title" => $persona->nome . " " . $persona->cognome, "subtitle" => $persona->nominativo])
  <div class="d-flex justify-content-end mb-3">
    <a
      class="btn btn-outline-primary"
      href="{{ route("anagrafe.person.show", $persona->id) }}"
    >
      Scheda anagrafica
    </a>
  </div>
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
                    Inserire la data di uscita di {{ $persona->nominativo }}
                  </p>
                  <input class="form-control" type="date" name="data_uscita" />
                </form>
              </x-slot:body>
              <x-slot:footer>
                <button
                  class="btn btn-success"
                  form="formUscitaPersona{{ $persona->id }}"
                >
                  Salva
                </button>
              </x-slot:footer>
            </x-modal>
          @else
            @if ($persona->getDataUscitaNomadelfia())
              <h2>
                Stato Attuale:
                <span class="badge bg-secondary">Uscito</span>
              </h2>
              <p>
                Uscita in data {{ $persona->getDataUscitaNomadelfia()->toDateString() }}
                <span class="badge text-bg-info">
                  @diffHumans($persona->getDataUscitaNomadelfia())
                </span>
              </p>
            @else
              <h2>
                Stato Attuale:
                <span class="badge bg-secondary">Mai entrato</span>
              </h2>
            @endif
            <x-modal
              modal-title="Entrata nella comunità"
              button-title="Entrata"
              button-style="btn-success my-2"
            >
              <x-slot:body>
                @include("nomadelfia.templates.entrataPersona", ["persona" => $persona])
              </x-slot:body>
            </x-modal>
          @endif

          <a
            class="btn btn-warning"
            href="{{ route("nomadelfia.join-leave-history.index", $persona->id) }}"
          >
            Vedi Storico
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
                  action="{{ route("nomadelfia.death.store", $persona->id) }}"
                >
                  @csrf
                  <p>
                    Inserire la data di decesso di {{ $persona->nominativo }}
                  </p>
                  <input class="form-control" type="date" name="data_decesso" />
                </form>
              </x-slot:body>
              <x-slot:footer>
                <button
                  class="btn btn-danger"
                  form="formDecessoPersona{{ $persona->id }}"
                >
                  Salva
                </button>
              </x-slot:footer>
            </x-modal>
          @else
            <span class="badge bg-secondary">
              Deceduto il {{ $persona->data_decesso }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-7 mb-3">
      <div class="card">
        <div class="card-header">Dati Nomadelfia</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold">Origine:</label>
                <div class="col-sm-6">
                  @if ($persona->origine)
                    <span> {{ $persona->origine }} </span>
                  @else
                    <span class="text-danger"> Nessuna origine </span>
                  @endif
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Numero Elenco: </label>
                <div class="col-sm-6">
                  @if ($persona->numero_elenco)
                    <span class="badge text-bg-info">
                      {{ $persona->numero_elenco }}
                    </span>
                  @else
                    <p class="text-danger">Nessun numero elenco</p>
                  @endif
                </div>
                <div class="col-sm-2">
                  @if (! $persona->numero_elenco)
                    <a
                      class="btn btn-warning"
                      href="{{ route("nomadelfia.folder-number.create", $persona->id) }}"
                      role="button"
                    >
                      Assegna
                    </a>
                  @endif
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Stato familiare: </label>
                <div class="col-sm-6">
                  @if ($persona->statoAttuale() != null)
                    <span> {{ $persona->statoAttuale()->nome }} </span>
                  @else
                    <span class="text-danger"> Nessuno stato </span>
                  @endif
                </div>
                <div class="col-sm-2">
                  <a
                    class="btn btn-warning"
                    href="{{ route("nomadelfia.persone.stato", $persona->id) }}"
                  >
                    Modifica
                  </a>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Posizione: </label>
                <div class="col-sm-6">
                  @if ($posizioneAttuale != null)
                    {{ $posizioneAttuale->nome }}
                  @else
                    <span class="text-danger"> Nessuna posizione </span>
                  @endif
                </div>
                <div class="col-sm-2">
                  <a
                    class="btn btn-warning"
                    href="{{ route("nomadelfia.person.position.index", $persona->id) }}"
                  >
                    Modifica
                  </a>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Gruppo familiare: </label>
                <div class="col-sm-6">
                  @if ($gruppoAttuale)
                    <a
                      href="{{ route("nomadelfia.gruppifamiliari.show", [$gruppoAttuale->id]) }}"
                    >
                      {{ $gruppoAttuale->nome }}
                    </a>
                  @else
                    <span class="text-danger"> Nessun gruppo </span>
                  @endif
                </div>
                <div class="col-sm-2">
                  <a
                    class="btn btn-warning"
                    href="{{ route("nomadelfia.person.gruppo", $persona->id) }}"
                  >
                    Modifica
                  </a>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Azienda/e: </label>
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
                    <span class="text-danger"> Nessuna azienda </span>
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
                <label class="col-sm-4 fw-bold"> Incarichi: </label>
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
                    <span class="text-danger"> Nessun incarico </span>
                  @endforelse
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-5 mb-3">
      <div class="card">
        <div class="card-header">Famiglia</div>
        <div class="card-body">
          @if ($famigliaEnrico)
            <div class="alert alert-warning" role="alert">
              <strong>ALFA Enrico:</strong>
              {{ Illuminate\Support\Str::headline($famigliaEnrico->famiglia) }}
            </div>
          @endif

          @if ($famigliaAttuale)
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <div class="row">
                  <label class="col-sm-4 fw-bold"> Nome Famiglia: </label>
                  <div class="col-sm-8">
                    <a
                      href="{{ route("anagrafe.families.show", $famigliaAttuale->id) }}"
                    >
                      {{ $famigliaAttuale->nome_famiglia }}
                    </a>
                  </div>
                </div>
              </li>
              <li class="list-group-item">
                <div class="row">
                  <label class="col-sm-4 fw-bold"> Posizione: </label>
                  <div class="col-sm-8">
                    {{ $famigliaAttuale->pivot->posizione_famiglia }}
                  </div>
                </div>
              </li>
            </ul>
          @else
            <span class="text-danger">Nessuna famiglia</span>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
