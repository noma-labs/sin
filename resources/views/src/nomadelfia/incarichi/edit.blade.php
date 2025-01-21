@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Modifica Incarico: " . $incarico->nome])
    <div>
        <div class="row">
            <div class="col-md-8 table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-inverse">
                        <th scope="col">Nominativo</th>
                        <th scope="col">Data Inizio</th>
                        <th scope="col">Operazioni</th>
                    </thead>
                    <tbody>
                        @foreach ($lavoratori as $lavoratore)
                            <tr>
                                <td>{{ $lavoratore->nominativo }}</td>
                                <td>{{ $lavoratore->pivot->data_inizio }}</td>
                                <td>
                                    <x-modal
                                        modal-title="Elimina persona"
                                        button-title="Elimina"
                                        button-style="btn-danger my-2"
                                    >
                                        <x-slot:body>
                                            <form
                                                class="form"
                                                method="POST"
                                                id="formEliminaPersona{{ $lavoratore->id }}"
                                                action="{{ route("nomadelfia.incarichi.persone.elimina", ["idPersona" => $lavoratore->id, "id" => $incarico->id]) }}"
                                            >
                                                @csrf
                                                @method("delete")
                                                <body>
                                                    Vuoi davvero eliminare
                                                    {{ $lavoratore->nominativo }}
                                                    dall'incarico
                                                    {{ $incarico->nome }} ?
                                                </body>
                                            </form>
                                        </x-slot>
                                        <x-slot:footer>
                                            <button
                                                class="btn btn-danger"
                                                form="formEliminaPersona{{ $lavoratore->id }}"
                                            >
                                                Elimina
                                            </button>
                                        </x-slot>
                                    </x-modal>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-2 offset-sm-10">
                        @include("nomadelfia.templates.aggiungiPersonaIncarico", ["incarico" => $incarico])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
