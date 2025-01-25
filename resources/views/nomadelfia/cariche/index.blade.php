@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Cariche costituzionali"])
    <div class="row row-cols-1 row-cols-md-4">
        <div class="col">
            <div class="card">
                <div class="card-header">Associazione Nomadelfia</div>
                <div class="card-body">
                    <ul>
                        @foreach ($ass as $key => $membri)
                            <li>{{ $key }}</li>
                            <ul>
                                @foreach ($membri as $m)
                                    {{-- <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li> --}}
                                    <li>{{ $m->nominativo }}</li>
                                @endforeach
                            </ul>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route("nomadelfia.cariche.elezioni") }}"
                        class="btn btn-primary"
                    >
                        Elenco Elezioni
                    </a>
                    {{-- <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Modifica</a> --}}
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">Solidarietà Nomadelfia ODV</div>
                <div class="card-body">
                    <ul>
                        @foreach ($sol as $key => $membri)
                            <li>{{ $key }}</li>
                            <ul>
                                @foreach ($membri as $m)
                                    <li>
                                        @if (empty($m->nominativo))
                                            I have NO record!
                                        @else
                                            {{-- @include("nomadelfia.templates.persona", ['persona' => $m]) --}}
                                            <li>{{ $m->nominativo }}</li>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route("nomadelfia.famiglie") }}"
                        class="btn btn-primary"
                    >
                        Modifica
                    </a>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">Fondazione Nomadelfia</div>
                <div class="card-body">
                    <ul>
                        @foreach ($fon as $key => $membri)
                            <li>{{ $key }}</li>
                            <ul>
                                @foreach ($membri as $m)
                                    {{-- <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li> --}}
                                    <li>{{ $m->nominativo }}</li>
                                @endforeach
                            </ul>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route("nomadelfia.famiglie") }}"
                        class="btn btn-primary"
                    >
                        Modifica
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card">
                <div class="card-header">Cooperativa Agricola Culturale</div>
                <div class="card-body">
                    <ul>
                        @foreach ($agr as $key => $membri)
                            <li>{{ $key }}</li>
                            <ul>
                                @foreach ($membri as $m)
                                    {{-- <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li> --}}
                                    <li>{{ $m->nominativo }}</li>
                                @endforeach
                            </ul>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route("nomadelfia.famiglie") }}"
                        class="btn btn-primary"
                    >
                        Modifica
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
