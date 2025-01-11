@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Modifica azienda: " . $azienda->nome_azienda])

    <div class="row">
        <div class="col-md-8 table-responsive">
          <table class="table table-hover table-bordered">
            <thead class="thead-inverse">
              <th scope="col" width="40%">Nominativo</th>
              <th scope="col" width="15%" class="text-center">Stato</th>
              <th scope="col" width="20%">Data inizio lavoro</th>
              <th scope="col" width="25%">Operazioni</th>
            </thead>
            <tbody>
                @foreach ($azienda->lavoratoriAttuali as $lavoratore)
                    <tr id="{{ $lavoratore->id }}" hoverable>
                        <td scope="row">
                            {{ $lavoratore->nominativo }}
                            <span class="badge badge-warning">{{ $lavoratore->pivot->mansione }}</span
                          </td>
                          <td class="text-center">
                            <span  class="badge badge-primary">{{$lavoratore->pivot->stato }}</span>
                          </td>
                          <td>
                            {{ $lavoratore->pivot->data_inizio_azienda }}
                          </td>
                          <td class="text-center">
                            @include("nomadelfia.templates.modificaPersonaAzienda")
                            @include("nomadelfia.templates.spostaPersonaAzienda")
                            <form
                                class="form"
                                method="POST"
                                action="{{ route("nomadelfia.aziende.persona.delete", ["idPersona" => $lavoratore->id, "id" => $azienda->id]) }}"
                            >
                                @csrf
                                @method("delete")
                                <button
                                type="submit"
                                value="Submit"
                                class="btn btn-danger m-1"
                            >
                                Elimina
                            </button>
                            </form>
                          </td>
                    </tr>
                @endforeach
            </tbody>
          </table>
          <div class="row">
            <div class="col-sm-2 offset-sm-10">
                @include("nomadelfia.templates.aggiungiPersonaAzienda")
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">Hanno lavorato:</div>
            <div class="card-body">
              <div class="list-group list-group-flush">
                @foreach($azienda->lavoratoriStorici as $lavoratore)
                    <a
                        class="list-group-item list-group-item-secondary d-flex align-items-center list-group-item-action"
                        id="{{ $lavoratore->id }}"
                    >
                        {{ $lavoratore->nominativo }}
                        <span class="badge badge-danger badge-pill ml-4">
                            {{ $lavoratore->pivot->data_fine_azienda }}
                        </span>
                        <span class="badge badge-danger badge-pill ml-1">
                            {{ $lavoratore->pivot->stato }}
                        </span>
                    </a>
                @endforeach

              </div>
            </div>
          </div>
        </div>
      </div>

    <azienda-edit
        url_aggiungi="{{ route("api.nomadelfia.azienda.aggiungi.lavoratore") }}"
        base_url="/api/nomadelfia/aziende"
        url_azienda_edit="{{ route("api.nomadeflia.azienda.edit", $azienda->id) }}"
        url_persona
        url_mansioni="{{ route("api.nomadeflia.azienda.mansioni") }}"
        url_stati="{{ route("api.nomadeflia.azienda.stati") }}"
        url_modifica_lavoratore="{{ route("api.nomadeflia.azienda.modifica.lavoratore") }}"
        id_azienda="{{ $azienda->id }}"
    ></azienda-edit>
@endsection
