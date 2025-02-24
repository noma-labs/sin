@extends("patente.index")

@section("content")
    @include("partials.header", ["title" => "Ricerca Patenti " . "(numero patenti: " . App\Patente\Models\Patente::count() . ")"])

    <form method="GET" action="{{ route("patente.ricerca.conferma") }}">
        @csrf
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Persona</label>
                <livewire:search-persona name_input="persona_id" />
            </div>
            <div class="col-md-6">
                <label for="numero_patente">Numero Patente</label>
                <input
                    class="form-control"
                    id="numero_patente"
                    name="numero_patente"
                    placeholder="---Inserisci numero patente---"
                />
            </div>

            <div class="col-md-6">
                <label class="form-label">Data Scadenza patente</label>
                <select
                    class="form-select"
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
            <div class="col-md-3 d-flex align-items-end">
                <input type="date" class="form-control" name="data_scadenza" />
            </div>

            <div class="col-md-3">
                <label class="form-label" for="categoria_patente">
                    Categoria patente
                </label>
                <select
                    class="form-select"
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
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="categoria_patente">C.Q.C</label>
                <select class="form-select" id="cqc_patente" name="cqc_patente">
                    <option selected value="">---Scegli C.Q.C---</option>
                    @foreach ($cqc as $c)
                        <option value="{{ $c->id }}">
                            {{ $c->categoria }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Data Scadenza C.Q.C</label>
                <select
                    class="form-select"
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
            <div class="col-md-3 d-flex align-items-end">
                <input
                    type="date"
                    class="form-control"
                    name="cqc_data_scadenza"
                />
            </div>
        </div>
        <div class="row mb-3">
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Ricerca</button>
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

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"
            ></button>
        </div>
    @endif

    @if (! empty($patenti))
        <div id="results" class="alert alert-success">
            Numero di patenti trovate:
            <strong>{{ $patenti->total() }}</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr class="table-warning">
                        <th>Nome Cognome</th>
                        <th>
                            {{ App\Traits\SortableTrait::link_to_sorting_action("numero_patente", "N. Patente") }}
                        </th>
                        <th>Scadenza"</th>
                        <th>Categorie</th>
                        <th>Scadenza C.Q.C M.</th>
                        <th>Scadenza C.Q.C P.</th>
                        <th>Operazioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($patenti as $patente)
                        <tr class="table-primary" hoverable>
                            <td>
                                @if ($patente->persona->nome == null or $patente->persona->cognome == null)
                                    {{ $patente->persona->nominativo }}
                                @else
                                    {{ $patente->persona->nome }}
                                    {{ $patente->persona->cognome }}
                                @endif
                                @if ($patente->hasCommissione())
                                    <span class="badge bg-warning">C.</span>
                                @endif

                                @isset($patente->note)
                                    <span class="badge bg-success">N.</span>
                                @endisset
                            </td>
                            <td>{{ $patente->numero_patente }}</td>
                            <td>{{ $patente->data_scadenza_patente }}</td>
                            <td>{{ $patente->categorieAsString() }}</td>
                            <td>
                                @if ($patente->cqcMerci() !== null)
                                    {{ $patente->cqcMerci()->pivot->data_scadenza }}
                                @else
                                        ---
                                @endif
                            </td>
                            <td>
                                @if ($patente->cqcPersone() !== null)
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
                                    @can("scuolaguida.visualizza")
                                        <a
                                            class="btn btn-warning"
                                            href="{{ route("patente.visualizza", $patente->numero_patente) }}"
                                        >
                                            Dettaglio
                                        </a>
                                    @endcan

                                    @can("scuolaguida.elimina")
                                        <x-modal
                                            modal-title="Eliminazione patente"
                                            button-title="Elimina"
                                        >
                                            <x-slot:body>
                                                Vuoi davvero eliminare la
                                                patente di
                                                @isset($patente->persona->nome)
                                                    {{ $patente->persona->nome }}
                                                @endisset

                                                @isset($patente->persona->cognome)
                                                    {{ $patente->persona->cognome }}
                                                @endisset

                                                ?
                                            </x-slot>

                                            <x-slot:footer>
                                                <a
                                                    class="btn btn-danger"
                                                    href="{{ route("patente.elimina", $patente->numero_patente) }}"
                                                >
                                                    Elimina
                                                </a>
                                            </x-slot>
                                        </x-modal>
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
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    Vuoi davvero eliminare la patente ?
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
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
