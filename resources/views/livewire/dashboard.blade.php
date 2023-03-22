<input class="form-control" wire:model="search">
<table id="table" class='table table-bordered table-hover table-sm'>
    <thead class='thead-inverse'>
    <tr>
        <th style="width:2%" style="font-size:10px">Num. Elenco</th>
        <th style="width:10%" style="font-size:10px">Nominativo</th>
        <th style="width:10%" style="font-size:10px">Nome</th>
        <th style="width:10%" style="font-size:10px">Cognome</th>
        <th style="width:10%" style="font-size:10px"></th>
        <th style="width:10%" style="font-size:10px">Luogo Nascita</th>
        <th style="width:12%" style="font-size:10px">Posizione Nomadelfia</th>
        <th style="width:12%" style="font-size:10px">Data Entrata</th>
        <th style="width:18%" style="font-size:10px">Operazioni</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($popolazione as $persona)
        <tr>
            <td><span class="badge badge-warning">{{$persona->numero_elenco}}</span></td>
            <td>{{ $persona->nominativo }} </td>
            <td>{{ $persona->nome }} </td>
            <td>{{ $persona->cognome }}</td>
            <td>{{ $persona->data_nascita }}
                <span class="badge badge-secondary">@diffYears($persona->data_nascita) anni</span>
            </td>
            <td>
                @if ($persona->posizione)
                    {{ $persona->posizione }}
                @endif
            </td>
            <td> {{ $persona->data_entrata }} </td>
            <td> {{ $persona->provincia_nascita }} </td>
            <td>
                <a class="btn btn-warning btn-sm"
                   href="{{ route('nomadelfia.persone.dettaglio',['idPersona'=> $persona->id]) }}">Dettaglio</a>
            </td>
        </tr>
    @empty
        <div class="alert alert-danger">
            <strong>Nessun risultato ottenuto</strong>
        </div>
    @endforelse

    </tbody>
</table>