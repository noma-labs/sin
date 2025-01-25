@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Incarichi"])

    @include("nomadelfia.templates.aggiungiIncarico")


    @if($busy->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h4 class="card-title">Suggerimenti</h4>
                <b>Queste persone hanno {{ $min }} o più incarichi:</b>
                <ul class="list-group list-group-flush">
                    @forelse ($busy as $b)
                        <li class="list-group-item">
                            @include("nomadelfia.templates.persona", ["persona" => $b])
                            <span class="badge bg-primary">
                                {{ $b->count }}
                            </span>
                        </li>
                    @empty
                        <p>Nessuna persona</p>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-success" role="alert">
        Nessuna persona ha {{ $min }} o più incarichi
    </div>
    @endif

    <div class="row row-cols-1 row-cols-md-3 g-3">
            @foreach ($incarichi as $incarico)
                <div class="col-md-4">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $incarico->id }}"
                            >
                                <h5 class="mb-0">
                                    <button
                                        class="btn btn-link"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $incarico->id }}"
                                        aria-expanded="true"
                                        aria-controls="collapse{{ $incarico->id }}"
                                    >
                                        {{ $incarico->nome }}
                                        <span
                                            class="badge bg-primary rounded-pill"
                                        >
                                            {{ $incarico->lavoratoriAttuali->count() }}
                                        </span>
                                    </button>
                                </h5>
                            </div>

                            <div
                                id="collapse{{ $incarico->id }}"
                                class="collapse"
                                aria-labelledby="heading{{ $incarico->id }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <ul>
                                        @forelse ($incarico->lavoratoriAttuali as $lavoratore)
                                            <li>
                                                @include("nomadelfia.templates.persona", ["persona" => $lavoratore])
                                            </li>
                                        @empty
                                            <p class="text-danger">
                                                Nessuna Persona
                                            </p>
                                        @endforelse
                                    </ul>
                                </div>
                                <div class="card-footer">
                                    <div
                                        class="row justify-content-between"
                                    >
                                        <div class="col-4">
                                            <a
                                                class="btn btn-warning"
                                                type="button"
                                                href="{{ route("nomadelfia.incarichi.edit", $incarico->id) }}"
                                            >
                                                Modifica
                                            </a>
                                        </div>
                                        <div class="col-4">
                                            @include("nomadelfia.templates.eliminaIncarico", ["incarico" => $incarico])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end card -->
                </div>
            @endforeach
    </div>
@endsection
