@extends("patente.index")

@section("content")
    @include("partials.header", ["title" => "Scadenze patenti", "subtitle" => "(numero patenti: " . App\Patente\Models\Patente::count() . ")"])

    <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Patenti</div>
                <div class="card-body">
                    <h5 class="card-title">
                        In scadenza entro
                        {{ config("patente.scadenze.patenti.inscadenza") }}
                        gg ({{ $patenti->count() }})
                    </h5>
                    <ul>
                        @foreach ($patenti as $patente)
                            <li>
                                <a
                                    href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                >
                                    {{ $patente->persona->nominativo }}
                                </a>
                                <span class="badge bg-warning">
                                    {{ $patente->data_scadenza_patente }}
                                    ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->data_scadenza_patente)) }}gg)
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <h5 class="card-title">
                        Scadute ({{ $patentiScadute->count() }})
                    </h5>
                    <ul>
                        @foreach ($patentiScadute as $patente)
                            <li>
                                <a
                                    href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                >
                                    {{ $patente->persona->nominativo }}
                                </a>
                                <span class="badge bg-danger">
                                    {{ $patente->data_scadenza_patente }}
                                    ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->data_scadenza_patente)) }}
                                    gg)
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-header">Patenti con commissione</div>
                <div class="card-body">
                    <h5 class="card-title">
                        In scadenza entro
                        {{ config("patente.scadenze.commissione.inscadenza") }}
                        gg ({{ $patentiCommissione->count() }})
                    </h5>
                    <ul>
                        @foreach ($patentiCommissione as $patente)
                            <li>
                                <a
                                    href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                >
                                    {{ $patente->persona->nominativo }}
                                </a>
                                <span class="badge bg-warning">
                                    {{ $patente->data_scadenza_patente }}
                                    ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->data_scadenza_patente)) }}
                                    gg)
                                </span>
                            </li>
                        @endforeach
                    </ul>
                    <h5 class="card-title">
                        Scadute ({{ $patentiCommisioneScadute->count() }})
                    </h5>
                    <ul>
                        @foreach ($patentiCommisioneScadute as $patente)
                            <li>
                                <a
                                    href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                >
                                    {{ $patente->persona->nominativo }}
                                </a>
                                <span class="badge bg-danger">
                                    {{ $patente->data_scadenza_patente }}
                                    ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->data_scadenza_patente)) }}
                                    gg)
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-header">C.Q.C</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">C.Q.C Persone</h5>
                            <h6 class="card-subtitle">
                                In Scadenza entro
                                {{ config("patente.scadenze.cqc.inscadenza") }}
                                gg ({{ $patentiCQCPersone->count() }})
                            </h6>
                            <ul>
                                @foreach ($patentiCQCPersone as $patente)
                                    <li>
                                        <a
                                            href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                        >
                                            {{ $patente->persona->nominativo }}
                                        </a>
                                        <span class="badge bg-warning">
                                            {{ $patente->pivot->data_scadenza }}
                                            ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->pivot->data_scadenza)) }}gg)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <h6 class="card-subtitle">
                                Scadute
                                ({{ $patentiCQCPersoneScadute->count() }})
                            </h6>
                            <ul>
                                @foreach ($patentiCQCPersoneScadute as $patente)
                                    <li>
                                        <a
                                            href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                        >
                                            {{ $patente->persona->nominativo }}
                                        </a>
                                        <span class="badge bg-danger">
                                            {{ $patente->pivot->data_scadenza }}
                                            ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->pivot->data_scadenza)) }}gg)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">C.Q.C Merci</h5>
                            <h6 class="card-subtitle">
                                In scadenza entro
                                {{ config("patente.scadenze.cqc.inscadenza") }}
                                gg ({{ $patentiCQCMerci->count() }})
                            </h6>
                            <ul>
                                @foreach ($patentiCQCMerci as $patente)
                                    <li>
                                        <a
                                            href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                        >
                                            {{ $patente->persona->nominativo }}
                                        </a>
                                        <span class="badge bg-warning">
                                            {{ $patente->pivot->data_scadenza }}
                                            ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->pivot->data_scadenza)) }}gg)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <h6 class="card-subtitle">
                                Scadute
                                ({{ $patentiCQCMerciScadute->count() }})
                            </h6>
                            <ul>
                                @foreach ($patentiCQCMerciScadute as $patente)
                                    <li>
                                        <a
                                            href="{{ route("patente.visualizza", ["numero" => $patente->numero_patente]) }}"
                                        >
                                            {{ $patente->persona->nominativo }}
                                        </a>
                                        <span class="badge bg-danger">
                                            {{ $patente->pivot->data_scadenza }}
                                            ({{ Carbon::now("America/Vancouver")->diffInDays(Carbon::parse($patente->pivot->data_scadenza)) }}gg)
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr class="table-warning">
                <th>Nome Cognome</th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("numero_patente", "Num. Patente") }}
                </th>
                <th>
                    {{ App\Traits\SortableTrait::link_to_sorting_action("numero_patente", "Data Scadenza") }}
                </th>
                <th>Categorie</th>
                <th>Scadenza C.Q.C M.</th>
                <th>Scadenza C.Q.C P.</th>
                <th>Operazioni</th>
            </tr>
        </thead>
       <tbody>
            @foreach ($patentiAll as $patente)
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
                                    button-style="btn-danger"
                                >
                                    <x-slot:body>
                                        Vuoi davvero eliminare la patente di
                                        @isset($patente->persona->nome)
                                            {{ $patente->persona->nome }}
                                        @endisset

                                        @isset($patente->persona->cognome)
                                            {{ $patente->persona->cognome }}
                                        @endisset

                                        ?
                                    </x-slot>

                                    <x-slot:footer>
                                        <form
                                            action="{{ route("patente.elimina", $patente->numero_patente) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method("DELETE")
                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                            >
                                                Elimina
                                            </button>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-md-6 offset-md-4">
            {{ $patentiAll->appends(request()->except("page"))->links("vendor.pagination.bootstrap-5") }}
        </div>
    </div>
@endsection
