@extends("rtn.index")

@section("title", "Gestione Video Professionale")

@section("content")
    <h1>Archivio professionale</h1>
    <ul>
        @foreach ($countByYear as $c)
            <li>
                {{ $c->year }} {{ $c->month }}
                <span class="badge bg-secondary">{{ $c->count }}</span>
            </li>
        @endforeach
    </ul>
@endsection
