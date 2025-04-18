@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Gruppi Familiari"])

    @foreach (collect($g)->chunk(3) as $chunk)
        <div class="row">
            @foreach ($chunk as $gruppo)
                <div class="col-md-4 my-1">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $gruppo->id }}"
                            >
                                <h5 class="mb-0">
                                    <a
                                        class="btn btn-link"
                                        href="{{ route("nomadelfia.gruppifamiliari.show", $gruppo->id) }}"
                                    >
                                        {{ $gruppo->nome }}
                                    </a>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $gruppo->count }}
                                    </span>
                                </h5>
                            </div>
                            <div
                                id="collapse{{ $gruppo->id }}"
                                class="collapse"
                                aria-labelledby="heading{{ $gruppo->id }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <a
                                        class="btn btn-primary"
                                        href="{{ route("nomadelfia.gruppifamiliari.show", $gruppo->id) }}"
                                    >
                                        Dettaglio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
