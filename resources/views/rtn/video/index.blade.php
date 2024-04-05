@extends("rtn.index")

@section("title", "Gestione Video Professionale")

@section("archivio")
    <h1>Archivio professionale</h1>
    <ul>
        @foreach ($countByYear as $c)
            <li>
                {{ $c->year }} {{ $c->month }}
                <span class="badge badge-secondary">{{ $c->count }}</span>
            </li>
        @endforeach
    </ul>
@endsection
