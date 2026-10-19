@extends("nomadelfia.index")

@section("content")
  @include("partials.header", ["title" => $persona->nome . " " . $persona->cognome, "subtitle" => $persona->nominativo])
  <div class="row">
    <div class="col-md-6 mb-3">
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
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header">Dati Famiglia</div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            @if ($famigliaAttuale)
              <li class="list-group-item">
                <div class="row">
                  <label class="col-sm-4 fw-bold"> Nome Famiglia: </label>
                  <div class="col-sm-8">
                    <a
                      href="{{ route("anagrafe.families.show", $persona->famigliaAttuale()->id) }}"
                    >
                      {{ $persona->famigliaAttuale()->nome_famiglia }}
                    </a>
                  </div>
                </div>
              </li>
              <li class="list-group-item">
                <div class="row">
                  <label class="col-sm-4 fw-bold"> Posizione: </label>
                  <div class="col-sm-8">
                    <span>
                      {{ $persona->famigliaAttuale()->pivot->posizione_famiglia }}
                    </span>
                  </div>
                </div>
              </li>
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-8">
                    <a
                      class="btn btn-warning"
                      href="{{ route("anagrafe.person.families", $persona->id) }}"
                    >
                      Storico
                    </a>
                  </div>
                </div>
              </li>
            @else
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-8">
                    @include("nomadelfia.templates.addPersonToFamiliy", ["persona" => $persona])
                  </div>
                </div>
              </li>
            @endif
          </ul>
          @can("popolazione.persona.inserisci")
            @if ($persona->isMaggiorenne() && ! $persona->isDeceduta() && ! $persona->isCapoFamiglia() && ! $persona->isMoglie())
              <a
                class="btn btn-success my-2"
                href="{{ route("anagrafe.marriage.create", ["persona" => $persona->id]) }}"
              >
                Nuovo matrimonio
              </a>
            @endif
          @endcan
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-header">Nomadelfia</div>
        <div class="card-body">
          @if ($persona->isPersonaInterna())
            <p>
              <span class="badge bg-success">In Nomadelfia</span>
              dal {{ $persona->getDataEntrataNomadelfia()->toDateString() }}
            </p>
            <p>
              {{ $persona->posizioneAttuale()?->nome }}
              @if ($gruppo = $persona->gruppofamiliareAttuale())
                &middot; {{ $gruppo->nome }}
              @endif
            </p>
          @elseif ($persona->getDataUscitaNomadelfia())
            <p>
              <span class="badge bg-secondary">Uscito</span>
              il {{ $persona->getDataUscitaNomadelfia()->toDateString() }}
            </p>
          @else
            <p><span class="badge bg-secondary">Mai entrato</span></p>
          @endif

          @if ($persona->isPersonaInterna() || $persona->getDataUscitaNomadelfia())
            @can("popolazione.persona.visualizza")
              <a
                class="btn btn-outline-primary"
                href="{{ route("nomadelfia.person.show", $persona->id) }}"
              >
                Apri in Popolazione
              </a>
            @endcan
          @else
            @can("popolazione.persona.inserisci")
              <a
                class="btn btn-success"
                href="{{ route("nomadelfia.join.create", $persona->id) }}"
              >
                Entra in Nomadelfia
              </a>
            @endcan
          @endif
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
