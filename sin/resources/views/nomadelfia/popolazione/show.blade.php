@extends('nomadelfia.index')

@section('archivio')

    @include('partials.header', ['title' => 'Gestione Popolazione ' . count($popolazione)])

    <table id="table" class='table table-bordered table-hover table-sm'>
        <thead class='thead-inverse'>
        <tr>
            <th style="width:10%" style="font-size:10px">Nominativo</th>
            <th style="width:10%" style="font-size:10px">Nome</th>
            <th style="width:30%" style="font-size:10px">Cognome</th>
            <th style="width:10%" style="font-size:10px">{{ App\Traits\SortableTrait::link_to_sorting_action('data_nascita',"Data Nascita") }}</th>
            <th style="width:12%" style="font-size:10px">Posizione Nomadelfia</th>
            <th style="width:12%" style="font-size:10px">Data Entrata</th>
            <th style="width:18%" style="font-size:10px">Operazioni</th>
        </tr>
        </thead>
        <tbody>

        @forelse ($popolazione as $persona)
            <tr>
                <td>{{ $persona->nominativo }} </td>
                <td>{{ $persona->nome }} </td>
                <td>{{ $persona->cognome }}</td>
                <td>{{ $persona->data_nascita }}</td>
                <td>
                    @if ($persona->posizione)
                        {{ $persona->posizione }}
                    @endif
                </td>
                <td> {{ $persona->data_entrata }} </td>
                <td>
                    <a class="btn btn-warning btn-sm" href="{{ route('nomadelfia.persone.dettaglio',['idPersona'=> $persona->id]) }}">Dettaglio</a>

                </td>
            </tr>
        @empty
            <div class="alert alert-danger">
                <strong>Nessun risultato ottenuto</strong>
            </div>
        @endforelse
        </tbody>
    </table>
@endsection
