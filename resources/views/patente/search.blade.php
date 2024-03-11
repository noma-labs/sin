@extends("patente.index")

@section("archivio")
    <sin-header title="Ricerca Patenti">
        Numero patenti: {{ App\Patente\Models\Patente::count() }}
    </sin-header>

    <form method="GET" action="{{ route("patente.ricerca.conferma") }}">
        {{ csrf_field() }}
        <div class="form-row">
            <div class="form-group col-md-2 offset-md-1">
                <label>Persona</label>
                <autocomplete
                    placeholder="---Inserisci nome o cognome ---"
                    name="persona_id"
                    label="value"
                    index="persona_id"
                    url="{{ route("api.patente.persone.conpatente") }}"
                ></autocomplete>
            </div>
            <div class="form-group col-md-2">
                <label for="numero_patente">Numero Patente</label>
                <input
                    class="form-control"
                    id="numero_patente"
                    name="numero_patente"
                    placeholder="---Inserisci numero patente---"
                />
            </div>

            <div class="form-group col-md-2">
                <label class="control-label">Data Scadenza patente</label>
                <select
                    class="form-control"
                    name="criterio_data_scadenza"
                    type="text"
                >
                    <option selected value="">---Scegli criterio---</option>
                    <option value="<">Minore</option>
                    <option value="<=">Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=">Maggiore Uguale</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <input
                        type="date"
                        class="form-control"
                        name="data_scadenza"
                    />
                </div>
            </div>

            <div class="form-group col-md-2">
                <label for="categoria_patente">Categoria patente</label>
                <select
                    class="form-control"
                    id="categoria_patente"
                    name="categoria_patente"
                >
                    <option selected value="">---Scegli categoria---</option>
                    @foreach ($categorie as $categoria)
                        <option value="{{ $categoria->id }}">
                            {{ $categoria->categoria }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row">
            {{--
                <div class="form-group col-md-2  offset-md-1">
                <label class="control-label">Data Rilascio</label>
                <select class="form-control" name="criterio_data_rilascio" type="text">
                <option selected value="">---Scegli criterio---</option>
                <option value="<">Minore</option>
                <option value="<=">Minore Uguale</option>
                <option value="=">Uguale</option>
                <option value=">">Maggiore</option>
                <option value=">=">Maggiore Uguale</option>
                </select>
                </div>
                <div class="col-md-2">
                <div class="form-group">
                <label>&nbsp;</label>
                <input type="date" class="form-control" name="data_rilascio">
                </div>
                </div>
            --}}
            <div class="form-group col-md-2 offset-md-1">
                <label for="categoria_patente">C.Q.C</label>
                <select
                    class="form-control"
                    id="cqc_patente"
                    name="cqc_patente"
                >
                    <option selected value="">---Scegli C.Q.C---</option>
                    @foreach ($cqc as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->categoria }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-2">
                <label class="control-label">Data Scadenza C.Q.C</label>
                <select
                    class="form-control"
                    name="criterio_cqc_data_scadenza"
                    type="text"
                >
                    <option selected value="">---Scegli criterio---</option>
                    <option value="<">Minore</option>
                    <option value="<=">Minore Uguale</option>
                    <option value="=">Uguale</option>
                    <option value=">">Maggiore</option>
                    <option value=">=">Maggiore Uguale</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <input
                        type="date"
                        class="form-control"
                        name="cqc_data_scadenza"
                    />
                </div>
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-block btn-primary">
                    Ricerca
                </button>
            </div>
        </div>
    </form>

    @if (! empty($msgSearch))
        <div
            class="alert alert-warning alert-dismissible fade show"
            role="alert"
        >
            Ricerca effettuata:
            <strong>{{ $msgSearch }}</strong>
            <a href="#" class="close" data-dismiss="alert" aria-label="close">
                &times;
            </a>
        </div>
    @endif

    @if (! empty($patenti))
        <div id="results" class="alert alert-success">
            Numero di patenti trovate:
            <strong>{{ $patenti->total() }}</strong>
        </div>

        <div class="table-responsive">
            <table
                class="table table-hover table-bordered table-sm"
                style="table-layout: auto; overflow-x: scroll"
            >
                <thead class="thead-inverse">
                    <tr>
                        <th style="width: 20%">Nome Cognome</th>
                        <th style="width: 10%">
                            {{ App\Traits\SortableTrait::link_to_sorting_action("numero_patente", "N. Patente") }}
                        </th>
                        <th style="width: 10%">
                            {{
                                App\Traits\SortableTrait::link_to_sorting_action(
                                    "numero_patente",
                                    "Data
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                Scadenza",
                                )
                            }}
                        </th>
                        <th style="width: 20%">Categorie</th>
                        <th style="width: 15%">Scadenza C.Q.C M.</th>
                        <th style="width: 15%">Scadenza C.Q.C P.</th>
                        <th style="width: 10%">Operazioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patenti as $patente)
                        <tr hoverable>
                            <td>
                                @if ($patente->persona->nome == null or $patente->persona->cognome == null)
                                    {{ $patente->persona->nominativo }}
                                @else
                                    {{ $patente->persona->nome }}
                                    {{ $patente->persona->cognome }}
                                @endif
                                @if ($patente->hasCommissione())
                                    <span class="badge badge-warning">C.</span>
                                @endif

                                @isset($patente->note)
                                    <span class="badge badge-success">N.</span>
                                @endisset
                            </td>
                            <td>{{ $patente->numero_patente }}</td>
                            <td>{{ $patente->data_scadenza_patente }}</td>
                            <td>{{ $patente->categorieAsString() }}</td>
                            <td>
                                @if ($patente->hasCqcMerci())
                                    {{ $patente->cqcMerci()->pivot->data_scadenza }}
                                @else
                                        ---
                                @endif
                            </td>
                            <td>
                                @if ($patente->hasCqcPersone())
                                    {{ $patente->cqcPersone()->pivot->data_scadenza }}
                                @else
                                        ---
                                @endif
                            </td>
                            <td>
                                <div
                                    class="btn-group"
                                    role="group"
                                    aria-label="Basic example"
                                >
                                    @can("scuolaguida.modifica")
                                        <a
                                            class="btn btn-warning"
                                            href="{{ route("patente.modifica", $patente->numero_patente) }}"
                                        >
                                            Modifica
                                        </a>
                                    @endcan

                                    @can("scuolaguida.elimina")
                                        <my-modal
                                            modal-title="Eliminazione patente"
                                            button-title="Elimina"
                                        >
                                            <template slot="modal-body-slot">
                                                Vuoi davvero eliminare la
                                                patente di
                                                @isset($patente->persona->nome)
                                                    {{ $patente->persona->nome }}
                                                @endisset

                                                @isset($patente->persona->cognome)
                                                    {{ $patente->persona->cognome }}
                                                @endisset

                                                ?
                                            </template>

                                            <template slot="modal-button">
                                                <a
                                                    class="btn btn-danger"
                                                    href="{{ route("patente.elimina", $patente->numero_patente) }}"
                                                >
                                                    Elimina
                                                </a>
                                            </template>
                                        </my-modal>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="col-md-2 offset-md-5">
                {{ $patenti->links("pagination::bootstrap-4") }}
            </div>
        </div>
    @endif

    <!-- Modal -->

    <div
        class="modal fade"
        id="eliminaModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalEliminaPatente"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEliminaPatente">
                        Elimina Patente
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Vuo davvero eliminare la patente ?</div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
