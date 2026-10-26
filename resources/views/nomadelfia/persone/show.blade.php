@extends("nomadelfia.index")

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
          <a
            class="btn btn-outline-primary"
            href="{{ route("nomadelfia.person.profile", $persona->id) }}"
          >
            Prova la nuova scheda
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
                <label class="col-sm-4 fw-bold"> Codice Fiscale: </label>
                <div class="col-sm-8">
                  <span>
                    @if ($persona->cf)
                      {{ $persona->cf }}
                    @else
                      <p class="text-danger">Nessun codice fiscale</p>
                    @endif
                  </span>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Data Nascita: </label>
                <div class="col-sm-8">
                  <span> {{ $persona->data_nascita }} </span>
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Luogo Nascita: </label>
                <div class="col-sm-8">
                  <span> {{ $persona->provincia_nascita }} </span>
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
                  <label class="col-sm-4 fw-bold"> Data decesso: </label>
                  <div class="col-sm-8">
                    <span> {{ $persona->data_decesso }} </span>
                  </div>
                </div>
              </li>
            @endif

            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Biografia: </label>
                <div class="col-sm-8">
                  @if ($persona->biografia)
                    <textarea class="form-control" id="biografia" readonly>
 {{ $persona->biografia }}</textarea
                    >
                  @else
                    <p class="text-danger">Nessuna biografia</p>
                  @endif
                </div>
              </div>
            </li>
          </ul>
          <a
            class="btn btn-warning my-2"
            href="{{ route("anagrafe.person.identity.edit", $persona->id) }}"
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
                    <span> {{ $persona->origine }} </span>
                  @else
                    <span class="text-danger"> Nessuna origine </span>
                  @endif
                </div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <label class="col-sm-4 fw-bold"> Nominativo: </label>
                <div class="col-sm-6">{{ $persona->nominativo }}</div>
                <div class="col-sm-2">
                  <a
                    class="btn btn-warning"
                    href="{{ route("anagrafe.internal-name.edit", $persona->id) }}"
                    role="button"
                  >
                    Modifica
                  </a>
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
                  <span>
                    {{ collect([$persona->statoCivile()["nome"]])->merge($persona->statiAttivi()->get()->reject->isStatoCivile()->pluck("nome"))->push($persona->famigliaSenzaConiuge()?->tipo->nome())->filter()->implode(" · ") }}
                  </span>
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

    <div class="col-md-4">
      <div class="card mb-3">
        <div
          class="card-header d-flex justify-content-between align-items-center"
        >
          Dati Famiglia
          <a
            class="small"
            href="{{ route("anagrafe.person.families", $persona->id) }}"
          >
            Storico
          </a>
        </div>
        <div class="card-body">
          @if ($famigliaEnrico)
            <div class="alert alert-warning" role="alert">
              <strong>ALFA Enrico:</strong>
              {{ Illuminate\Support\Str::headline($famigliaEnrico->famiglia) }}
            </div>
          @endif

          @php
            $posizioneFamiglia = $famigliaAttuale?->pivot->posizione_famiglia;
            $inCoppia = in_array($posizioneFamiglia, ["CAPO FAMIGLIA", "MOGLIE"], true);
            $isFiglio = in_array($posizioneFamiglia, ["FIGLIO NATO", "FIGLIO ACCOLTO"], true);
          @endphp

          @if ($famigliaAttuale)
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <div class="row">
                  <label class="col-sm-4 fw-bold">Famiglia:</label>
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
                  <label class="col-sm-4 fw-bold">Posizione:</label>
                  <div class="col-sm-8">{{ $posizioneFamiglia }}</div>
                </div>
              </li>
              @if ($inCoppia)
                @php
                  $coniuge = $famigliaAttuale
                      ->componentiAttuali()
                      ->wherePivot("posizione_famiglia", $posizioneFamiglia === "CAPO FAMIGLIA" ? "MOGLIE" : "CAPO FAMIGLIA")
                      ->first();
                @endphp
                <li class="list-group-item">
                  <div class="row">
                    <label class="col-sm-4 fw-bold">Coniuge:</label>
                    <div class="col-sm-8">
                      @if ($coniuge)
                        <a
                          href="{{ route("nomadelfia.person.show", $coniuge->id) }}"
                        >
                          {{ $coniuge->nominativo }}
                        </a>
                      @else
                        <span class="text-muted">Nessuno</span>
                      @endif
                    </div>
                  </div>
                </li>
                <li class="list-group-item">
                  <div class="row">
                    <label class="col-sm-4 fw-bold">Figli:</label>
                    <div class="col-sm-8">
                      {{ $famigliaAttuale->figliAttuali()->count() }}
                    </div>
                  </div>
                </li>
              @elseif ($isFiglio)
                <li class="list-group-item">
                  <div class="row">
                    <label class="col-sm-4 fw-bold">Genitori:</label>
                    <div class="col-sm-8">
                      @foreach ($famigliaAttuale->componentiAttuali()->wherePivotIn("posizione_famiglia", ["CAPO FAMIGLIA", "MOGLIE"])->get() as $genitore)
                        <a
                          href="{{ route("nomadelfia.person.show", $genitore->id) }}"
                        >
                          {{ $genitore->nominativo }}
                        </a>
                        @if (! $loop->last)
                          &middot;
                        @endif
                      @endforeach
                    </div>
                  </div>
                </li>
              @endif
            </ul>
          @else
            <p class="text-muted">Nessuna famiglia</p>
          @endif

          @can("popolazione.persona.inserisci")
            <div class="d-flex flex-wrap gap-2 mt-2">
              @if ($persona->puoSposarsi())
                <a
                  class="btn btn-success"
                  href="{{ route("anagrafe.marriage.create", ["persona" => $persona->id]) }}"
                >
                  Nuovo matrimonio
                </a>
              @endif
              @if (! $famigliaAttuale)
                @include("nomadelfia.templates.addPersonToFamiliy", ["persona" => $persona])
              @endif
            </div>
          @endcan
        </div>
      </div>

      @can("scuola.visualizza")
        <div class="card">
          <div class="card-header">Scuola</div>
          <div class="card-body">
            <a href="{{ route("scuola.student.show", $persona->id) }}">
              Alunno
            </a>
          </div>
        </div>
      @endcan
    </div>
  </div>
@endsection
