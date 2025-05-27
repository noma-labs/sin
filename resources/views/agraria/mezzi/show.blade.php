@extends("agraria.index")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => $mezzo->nome])
    <div class="row">
        <div class="col-md-6">
            <div class="card card-mod mb-3">
                <div class="card-header">
                    <h3 class="card-title mb-0">Anagrafica Mezzo</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nome">Nome</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->nome }}"
                                disabled
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="numero_telaio">
                                Numero di Telaio
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->numero_telaio }}"
                                disabled
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="tot_ore">
                                Ore Totali
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                value="{{ $mezzo->tot_ore }}"
                                disabled
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="gomme_ant">
                                Gomme Anteriori
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                @if ($mezzo->gomme_ant)
                                    value="{{ $mezzo->gommeAnt->nome }}"
                                @endif
                                disabled
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="gomme_post">
                                Gomme Posteriori
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                @if ($mezzo->gomme_post)
                                    value="{{ $mezzo->gommePos->nome }}"
                                @endif
                                disabled
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="filtro_aria_int">
                                Filtro Aria Interno
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->filtro_aria_int }}"
                                disabled
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="filtro_aria_ext">
                                Filtro Aria Esterno
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->filtro_aria_ext }}"
                                disabled
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="filtro_olio">
                                Filtro Olio
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->filtro_olio }}"
                                disabled
                            />
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label" for="filtro_gasolio">
                                Filtro Gasolio
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->filtro_gasolio }}"
                                disabled
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="filtro_servizi">
                                Filtro Servizi
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $mezzo->filtro_servizi }}"
                                disabled
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <a
                                href="{{ route("agraria.vehicle.edit", ["id" => $mezzo->id]) }}"
                                class="btn btn-warning"
                            >
                                Modifica
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-mod mb-3">
                <div class="card-body text-center">
                    @php
                        $totaleOrdinarie = $historic
                            ->filter(function ($manutenzione) {
                                return $manutenzione->programmate && $manutenzione->programmate->count();
                            })
                            ->sum(function ($manutenzione) {
                                return is_numeric($manutenzione->spesa) ? $manutenzione->spesa : 0;
                            });

                        $totaleStraordinarie = $historic
                            ->filter(function ($manutenzione) {
                                return ! $manutenzione->programmate || ! $manutenzione->programmate->count();
                            })
                            ->sum(function ($manutenzione) {
                                return is_numeric($manutenzione->spesa) ? $manutenzione->spesa : 0;
                            });
                    @endphp

                    <div class="row">
                        <div class="col-6 border-end">
                            <h6 class="mb-1">Totale Manut. Ordinarie</h6>
                            <div class="fw-bold text-success">
                                €
                                {{ number_format($totaleOrdinarie, 2, ",", ".") }}
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">Totale Manut. Straordinarie</h6>
                            <div class="fw-bold text-danger">
                                €
                                {{ number_format($totaleStraordinarie, 2, ",", ".") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-mod">
                <div class="card-header">
                    <ul
                        class="nav nav-tabs card-header-tabs"
                        id="manutenzioniTabs"
                        role="tablist"
                    >
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link active"
                                id="prossime-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#prossime"
                                type="button"
                                role="tab"
                                aria-controls="prossime"
                                aria-selected="true"
                            >
                                Prossime Manutenzioni
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link"
                                id="storico-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#storico"
                                type="button"
                                role="tab"
                                aria-controls="storico"
                                aria-selected="false"
                            >
                                Storico Manutenzioni
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="manutenzioniTabsContent">
                        <div
                            class="tab-pane fade show active"
                            id="prossime"
                            role="tabpanel"
                            aria-labelledby="prossime-tab"
                        >
                            <table class="table table-hover table-bordered">
                                <thead class="table table-sm">
                                    <tr>
                                        <th scope="col" width="65%">Nome</th>
                                        <th scope="col" width="45%">
                                            Ore Mancanti
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($prossime as $k => $v)
                                        <tr>
                                            <td>{{ $k }}</td>
                                            @if ($v < 0)
                                                <td>
                                                    <span
                                                        class="badge bg-danger"
                                                    >
                                                        {{ "scaduto da: " . abs($v) }}
                                                    </span>
                                                </td>
                                            @elseif ($v < 50)
                                                <td>
                                                    <span
                                                        class="badge bg-warning"
                                                    >
                                                        {{ "scade tra: " . abs($v) }}
                                                    </span>
                                                </td>
                                            @else
                                                <td>
                                                    <span
                                                        class="badge bg-success"
                                                    >
                                                        {{ "scade tra: " . abs($v) }}
                                                    </span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div
                            class="tab-pane fade"
                            id="storico"
                            role="tabpanel"
                            aria-labelledby="storico-tab"
                        >
                            <table class="table table-hover table-bordered">
                                <thead class="table table-sm">
                                    <tr>
                                        <th scope="col">Data</th>
                                        <th scope="col">Ore</th>
                                        <th scope="col">Spesa</th>
                                        <th scope="col">Persona</th>
                                        <th scope="col">Lavori Extra</th>
                                        <th scope="col">Programmate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($historic as $manutenzione)
                                        <tr>
                                            <td>
                                                {{ $manutenzione->data ?? "" }}
                                            </td>
                                            <td>
                                                {{ $manutenzione->ore ?? "" }}
                                            </td>
                                            <td>
                                                {{ $manutenzione->spesa ?? "" }}
                                            </td>
                                            <td>
                                                {{ $manutenzione->persona ?? "" }}
                                            </td>
                                            <td>
                                                {{ $manutenzione->lavori_extra ?? "" }}
                                            </td>
                                            <td>
                                                @if ($manutenzione->programmate && $manutenzione->programmate->count())
                                                    <ul class="mb-0 ps-3">
                                                        @foreach ($manutenzione->programmate as $programmata)
                                                            <li>
                                                                {{ $programmata->nome ?? "" }}
                                                                @if (isset($programmata->pivot->data))
                                                                        ({{ $programmata->pivot->data }})
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">
                                                        -
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                Nessuna manutenzione storica
                                                trovata.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
