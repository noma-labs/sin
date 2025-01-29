@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Classi A.S. " . $anno->scolastico])

    @include("scuola.templates.aggiungiClasse", ["anno" => $anno])
    @foreach ($classi->chunk(3) as $chunk)
        <div class="row my-2">
            @foreach ($chunk as $classe)
                <div class="col-md-4">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $classe->id }}"
                            >
                                <h5 class="mb-0">
                                    <button
                                        class="btn btn-link"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $classe->id }}"
                                        aria-expanded="true"
                                        aria-controls="collapse{{ $classe->id }}"
                                    >
                                        {{ $classe->tipo->nome }}
                                        <span
                                            class="badge bg-primary rounded-pill"
                                        >
                                            {{ $classe->alunni()->count() }}
                                        </span>
                                    </button>
                                </h5>
                            </div>

                            <div
                                id="collapse{{ $classe->id }}"
                                class="collapse"
                                aria-labelledby="heading{{ $classe->id }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <div>Alunni</div>
                                    <ul>
                                        @foreach ($classe->alunni as $alunno)
                                            <li>
                                                @year($alunno->data_nascita)
                                                @include("nomadelfia.templates.persona", ["persona" => $alunno])
                                                @liveRome($alunno)
                                                    <span
                                                        class="badge bg-warning"
                                                    >
                                                        Roma
                                                    </span>
                                                @endliveRome
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a
                                        class="btn btn-primary"
                                        href="{{ route("scuola.anno.show", $classe->id) }}"
                                    >
                                        Dettaglio
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
