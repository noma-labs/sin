@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Modifica azienda: " . $azienda->nome_azienda])

    <div class="row">
        <div class="col-md-8 table-responsive">
            <table class="table table-hover bg-warning">
                <thead>
                    <tr class="table-warning">
                        <th scope="col">Nominativo</th>
                        <th scope="col" class="text-center">Stato</th>
                        <th scope="col" >Data inizio lavoro</th>
                        <th scope="col" >Operazioni</th>
                    </tr>
                </thead>
               <tbody>
                    @foreach ($azienda->lavoratoriAttuali as $lavoratore)
                        <tr class="table-primary"  id="{{ $lavoratore->id }}" hoverable>
                            <td scope="row">
                                {{ $lavoratore->nominativo }}
                                <span class="badge bg-warning">
                                    {{ $lavoratore->pivot->mansione }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">
                                    {{ $lavoratore->pivot->stato }}
                                </span>
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
                                        Concludi
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
                        @foreach ($azienda->lavoratoriStorici as $lavoratore)
                            <a
                                class="list-group-item list-group-item-secondary d-flex align-items-center list-group-item-action"
                                id="{{ $lavoratore->id }}"
                            >
                                {{ $lavoratore->nominativo }}
                                <span class="badge bg-danger rounded-pill ms-4">
                                    {{ $lavoratore->pivot->data_fine_azienda }}
                                </span>
                                <span class="badge bg-danger rounded-pill ms-1">
                                    {{ $lavoratore->pivot->stato }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
