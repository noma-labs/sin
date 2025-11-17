@extends('officina.index')

@section('title', 'Prenotazioni')

@section('content')
    @include('partials.header', ['title' => 'Aggiungi Prenotazioni'])

    @livewire('prenotazione-veicoli')

    <div class="table-responsive mb-3">
        <table class="table table-hover table-sm">
            <thead class="table-warning">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Stato</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Macchina</th>
                    <th scope="col">Data P./A.</th>
                    <th scope="col">Ora P.</th>
                    <th scope="col">Ora A.</th>
                    <th scope="col">Meccanico</th>
                    <th scope="col">Uso</th>
                    <th scope="col">Destinazione</th>
                    <th scope="col">Note</th>
                    <th scope="col">Oper.</th>
                </tr>
            </thead>
            <tbody class="table-primary">
                @foreach ($prenotazioni as $pren)
                    @empty($pren->delated_at)
                        <tr scope="row">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($pren->isPartita())
                                    <span class="badge bg-danger">Partita</span>
                                @elseif ($pren->deveAncoraPartire())
                                    <span class="badge bg-warning">
                                        Deve Partire
                                    </span>
                                @elseif ($pren->isArrivata())
                                    <span class="badge bg-success">
                                        Arrivata
                                    </span>
                                @endif
                            </td>
                            <td>{{ $pren->cliente->nominativo }}</td>
                            <td>
                                {{ $pren->veicolo->nome }}
                                @if ($pren->veicolo->deleted_at)
                                    <span class="badge bg-danger">
                                        demolito
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($pren->data_partenza == $pren->data_arrivo)
                                    {{ $pren->data_partenza }}
                                @else
                                    {{ $pren->data_partenza }}
                                    <br />
                                    {{ $pren->data_arrivo }}
                                @endif
                            </td>
                            <td>{{ $pren->ora_partenza }}</td>
                            <td>{{ $pren->ora_arrivo }}</td>
                            <td>{{ $pren->meccanico->nominativo }}</td>
                            <td>{{ $pren->uso->ofus_nome }}</td>
                            <td>{{ $pren->destinazione }}</td>
                            <td>{{ $pren->note }}</td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    @can('meccanica.prenotazione.modifica')
                                        <a class="btn btn-warning btn-sm"
                                            href="{{ route('officina.prenota.modifica', $pren->id) }}">
                                            Modifica
                                        </a>
                                    @endcan

                                    @can('meccanica.prenotazione.elimina')
                                        <form method="POST" action="{{ route('officina.prenota.delete', $pren->id) }}"
                                            style="display: inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Elimina
                                            </button>
                                        </form>
                                    @endcan

                                    @can('meccanica.veicolo.prenota')
                                        <button type="button" class="btn btn-info btn-sm">
                                            Clona
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endempty
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        <div class="btn-group btn-group-lg" role="group">
            <a role="button" href="{{ route('officina.index', ['day' => 'ieri']) }}"
                class="btn btn-danger @if ($day == 'ieri') active @endif">
                Ieri
            </a>
            <a role="button" href="{{ route('officina.index', ['day' => 'oggi']) }}"
                class="btn btn-success @if ($day == 'oggi') active @endif">
                Oggi
            </a>
            <a role="button" href="{{ route('officina.index', ['day' => 'all']) }}"
                class="btn btn-warning @if ($day == 'all') active @endif">
                Tutte
            </a>
        </div>
    </div>
@endsection
