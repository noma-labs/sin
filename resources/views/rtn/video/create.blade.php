@extends("rtn.index")

@section("title", "Inserisci Video")

@section("archivio")
    <h1>Archivio professionale</h1>

    <livewire:search-persona
        inputName="persona_id"
        placeholder="Cerca persona"
        noResultsMessage="Nessun risultato"
    />

    <ul>
        @foreach ($countByYear as $c)
            <li>
                {{ $c->year }} {{ $c->month }}
                <span class="badge badge-secondary">{{ $c->count }}</span>
            </li>
        @endforeach
    </ul>
@endsection
