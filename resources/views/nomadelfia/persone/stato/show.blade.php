@extends("nomadelfia.persone.index")

@section("content")
  @include("partials.header", ["title" => "Gestione Stato  " . $persona->nominativo])
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Stato civile</div>
        <div class="card-body">
          <div class="row">
            <p class="col-md-4">{{ $statoCivile["nome"] }}</p>
            <p class="col-md-4">
              dal {{ $statoCivile["dal"]->translatedFormat("j M Y") }}
            </p>
            <div class="col-md-4">
              @if ($matrimonio)
                <a
                  href="{{ route("anagrafe.families.show", ["id" => $matrimonio->id]) }}"
                >
                  Famiglia {{ $matrimonio->nome_famiglia }}
                </a>
              @endif
            </div>
          </div>
          <small class="text-muted">
            Celibe, nubile, sposato e vedovo derivano dalla famiglia: per
            cambiarli modifica il matrimonio o la famiglia.
          </small>
        </div>
      </div>

      <div class="card my-3">
        <div class="card-header">Stati</div>
        <div class="card-body">
          <div class="row">
            <p class="col-md-3 fw-bold">Stato</p>
            <p class="col-md-3 fw-bold">Data inizio</p>
            <p class="col-md-3 fw-bold">Data fine</p>
            <p class="col-md-3 fw-bold">Operazioni</p>
          </div>

          @forelse ($stati as $stato)
            <div class="row">
              <p class="col-md-3">
                {{ $stato->nome }}
                @if ($stato->pivot->stato == "1")
                  <span class="badge text-bg-success">Attivo</span>
                @endif
              </p>
              <p class="col-md-3">{{ $stato->pivot->data_inizio }}</p>
              <p class="col-md-3">{{ $stato->pivot->data_fine }}</p>
              <div class="col-md-3">
                <x-modal
                  modal-title="Modifica stato {{ $stato->nome }}"
                  button-title="Modifica"
                  button-style="btn-warning my-2"
                >
                  <x-slot:body>
                    <form
                      class="form"
                      method="POST"
                      id="formPersonaStatoModifica{{ $loop->index }}"
                      action="{{ route("nomadelfia.persone.stato.modifica", ["id" => $persona->id, "idStato" => $stato->id]) }}"
                    >
                      @method("PUT")
                      @csrf
                      <div class="row">
                        <label class="col-sm-6 form-label"> Data inizio </label>
                        <div class="col-sm-6">
                          <input
                            type="date"
                            name="data_inizio"
                            value="{{ $stato->pivot->data_inizio }}"
                            class="form-control"
                          />
                        </div>
                      </div>
                      <div class="row">
                        <label class="col-sm-6 form-label">
                          Data fine Stato
                        </label>
                        <div class="col-sm-6">
                          <input
                            type="date"
                            name="data_fine"
                            value="{{ $stato->pivot->data_fine }}"
                            class="form-control"
                          />
                        </div>
                      </div>
                      <div class="row">
                        <label class="col-sm-6 form-label">Stato</label>
                        <div class="col-sm-6">
                          <div class="form-check">
                            <input
                              class="form-check-input"
                              type="radio"
                              name="stato"
                              id="statoAttivo{{ $loop->index }}"
                              value="1"
                              @if ($stato->pivot->stato == "1") checked @endif
                            />
                            <label
                              class="form-check-label"
                              for="statoAttivo{{ $loop->index }}"
                            >
                              Attivo
                            </label>
                          </div>
                          <div class="form-check">
                            <input
                              class="form-check-input"
                              type="radio"
                              name="stato"
                              id="statoDisattivo{{ $loop->index }}"
                              value="0"
                              @if ($stato->pivot->stato == "0") checked @endif
                            />
                            <label
                              class="form-check-label"
                              for="statoDisattivo{{ $loop->index }}"
                            >
                              Disattivo
                            </label>
                          </div>
                        </div>
                      </div>
                    </form>
                  </x-slot:body>
                  <x-slot:footer>
                    <button
                      class="btn btn-success"
                      form="formPersonaStatoModifica{{ $loop->index }}"
                    >
                      Salva
                    </button>
                  </x-slot:footer>
                </x-modal>
              </div>
            </div>
          @empty
            <p class="text-muted">Nessuno stato</p>
          @endforelse

          <x-modal
            modal-title="Aggiungi Stato persona"
            button-title="Nuovo Stato"
            button-style="btn-success my-2"
          >
            <x-slot:body>
              <form
                class="form"
                method="POST"
                id="formPersonaStato"
                action="{{ route("nomadelfia.persone.stato.assegna", $persona->id) }}"
              >
                @csrf
                <div class="row">
                  <label class="col-sm-4 form-label">Stato</label>
                  <div class="col-sm-8">
                    <select name="stato_id" class="form-select">
                      <option value="">---seleziona Stato---</option>
                      @foreach (App\Nomadelfia\PopolazioneNomadelfia\Models\Stato::all() as $statoDisponibile)
                        <option value="{{ $statoDisponibile->id }}">
                          {{ $statoDisponibile->nome }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 form-label">Data inizio</label>
                  <div class="col-sm-8">
                    <input
                      type="date"
                      name="data_inizio"
                      class="form-control"
                      value="{{ old("data_inizio") }}"
                    />
                  </div>
                </div>
              </form>
            </x-slot:body>
            <x-slot:footer>
              <button class="btn btn-success" form="formPersonaStato">
                Salva
              </button>
            </x-slot:footer>
          </x-modal>
        </div>
      </div>
    </div>
  </div>
@endsection
