@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Elezioni Cariche costituzionali"])
    <div class="row">
        <div class="col-md-3 card-deck">
            <div class="card">
                <div class="card-header">
                    Eleggibili: Consiglio Degli anziani
                    <span class="badge badge-primary badge-pill">
                        {{ $anz->total }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                Uomini
                                <span class="badge badge-primary badge-pill">
                                    {{ count($anz->uomini) }}
                                </span>
                            </p>
                            <ul>
                                @foreach ($anz->uomini as $u)
                                    <li>
                                        @include("nomadelfia.templates.persona", ["persona" => $u])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <p>
                                Donne
                                <span class="badge badge-primary badge-pill">
                                    {{ count($anz->donne) }}
                                </span>
                            </p>
                            <ul>
                                @foreach ($anz->donne as $d)
                                    <li>
                                        @include("nomadelfia.templates.persona", ["persona" => $d])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a
                        href="{{ route("nomadelfia.cariche.esporta") }}"
                        class="btn btn-primary"
                    >
                        Esporta
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
