@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Popolazione Nomadelfia"])

    <div class="row my-2">
        <div class="col-md-4">
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="effettivi">
                        <h5 class="mb-0">
                            <button
                                class="btn btn-link"
                                data-toggle="collapse"
                                data-target="#effettivi"
                                aria-expanded="true"
                                aria-controls="effettivi"
                            >
                                Effettivi
                                <span class="badge badge-primary badge-pill">
                                    {{ $effettivi->total }}
                                </span>
                            </button>
                        </h5>
                    </div>
                    <div
                        id="effettivi"
                        class="collapse"
                        aria-labelledby="effettivi"
                        data-parent="#accordion"
                    >
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>
                                        Uomini
                                        <span
                                            class="badge badge-primary badge-pill"
                                        >
                                            {{ count($effettivi->uomini) }}
                                        </span>
                                    </h5>
                                    @foreach ($effettivi->uomini as $uomo)
                                        <div>
                                            @include("nomadelfia.templates.persona", ["persona" => $uomo])
                                            {{ $uomo->data_inizio }}
                                        </div>
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <h5>
                                        Donne
                                        <span
                                            class="badge badge-primary badge-pill"
                                        >
                                            {{ count($effettivi->donne) }}
                                        </span>
                                    </h5>
                                    @foreach ($effettivi->donne as $donna)
                                        <div>
                                            @include("nomadelfia.templates.persona", ["persona" => $donna])
                                            {{ $donna->data_inizio }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end nomadelfi effettivi card -->
            </div>
            <!-- end accordion -->
        </div>

        <div class="col-md-4">
            <div id="accordion">
                <div class="card">
                    <div class="card-header" id="postulanti">
                        <h5 class="mb-0">
                            <button
                                class="btn btn-link"
                                data-toggle="collapse"
                                data-target="#postulanti"
                                aria-expanded="true"
                                aria-controls="postulanti"
                            >
                                Postulanti
                                <span class="badge badge-primary badge-pill">
                                    {{ $postulanti->total }}
                                </span>
                            </button>
                        </h5>
                    </div>
                    <div
                        id="postulanti"
                        class="collapse"
                        aria-labelledby="postulanti"
                        data-parent="#accordion"
                    >
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>
                                        Uomini
                                        <span
                                            class="badge badge-primary badge-pill"
                                        >
                                            COUNT UOMINI
                                        </span>
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <h5>
                                        Donne
                                        <span
                                            class="badge badge-primary badge-pill"
                                        >
                                            COUNT DONNE
                                        </span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end nomadelfi effettivi card -->
            </div>
            <!-- end accordion -->
        </div>
    </div>

    <h1 class="display-5">Posizioni</h1>
    @foreach (Domain\Nomadelfia\PopolazioneNomadelfia\Models\Posizione::all()->chunk(3) as $chunk)
        <div class="row my-2">
            @foreach ($chunk as $posizione)
                <div class="col-md-4">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="head{{ $posizione->id }}"
                            >
                                <h5 class="mb-0">
                                    <button
                                        class="btn btn-link"
                                        data-toggle="collapse"
                                        data-target="#posizione{{ $posizione->id }}"
                                        aria-expanded="false"
                                        aria-controls="posizione{{ $posizione->id }}"
                                    >
                                        <span class="text-lowercase">
                                            {{ $posizione->nome }}
                                        </span>
                                        <span
                                            class="badge badge-primary badge-pill"
                                        >
                                            {{ $posizione->personeAttuale()->count() }}
                                        </span>
                                    </button>
                                </h5>
                            </div>
                            <div
                                id="posizione{{ $posizione->id }}"
                                class="collapse"
                                aria-labelledby="head{{ $posizione->id }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>
                                                Uomini
                                                <span
                                                    class="badge badge-primary badge-pill"
                                                >
                                                    {{ $posizione->personeAttuale()->uomini()->count() }}
                                                </span>
                                            </h5>
                                            @foreach ($posizione->personeAttuale()->uomini()->get()as $uomo)
                                                <div>
                                                    @include("nomadelfia.templates.persona", ["persona" => $uomo])
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-6">
                                            <h5>
                                                Donne
                                                <span
                                                    class="badge badge-primary badge-pill"
                                                >
                                                    {{ $posizione->personeAttuale()->donne()->count() }}
                                                </span>
                                            </h5>
                                            @foreach ($posizione->personeAttuale()->donne()->get()as $donna)
                                                <div>
                                                    @include("nomadelfia.templates.persona", ["persona" => $donna])
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end nomadelfi effettivi card -->
                    </div>
                    <!-- end accordion -->
                </div>
            @endforeach
        </div>
    @endforeach

    <h1 class="display-5">Stato Familiare</h1>
    @foreach (Domain\Nomadelfia\PopolazioneNomadelfia\Models\Stato;::orderby("nome")->get()->chunk(3) as $chunk)
        <div class="row my-2">
            @foreach ($chunk as $stato)
                <div class="col-md-4">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $stato->nome }}"
                            >
                                <h5 class="mb-0">
                                    <button
                                        class="btn btn-link"
                                        data-toggle="collapse"
                                        data-target="#familiare{{ $stato->id }}"
                                        aria-expanded="false"
                                        aria-controls="familiare{{ $stato->id }}"
                                    >
                                        <span class="text-lowercase">
                                            {{ $stato->nome }}
                                        </span>
                                        <span
                                            class="badge badge-primary badge-pill"
                                        >
                                            {{ $stato->personeAttuale()->count() }}
                                        </span>
                                    </button>
                                </h5>
                            </div>
                            <div
                                id="familiare{{ $stato->id }}"
                                class="collapse"
                                aria-labelledby="heading{{ $stato->nome }}"
                                data-parent="#accordion"
                            >
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>
                                                Uomini
                                                <span
                                                    class="badge badge-primary badge-pill"
                                                >
                                                    {{ $stato->personeAttuale()->uomini()->count() }}
                                                </span>
                                            </h5>
                                            @foreach ($stato->personeAttuale()->uomini()->get()as $uomo)
                                                <div>
                                                    @include("nomadelfia.templates.persona", ["persona" => $uomo])
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="col-md-6">
                                            <h5>
                                                Donne
                                                <span
                                                    class="badge badge-primary badge-pill"
                                                >
                                                    {{ $stato->personeAttuale()->donne()->count() }}
                                                </span>
                                            </h5>
                                            @foreach ($stato->personeAttuale()->donne()->get()as $donna)
                                                <div>
                                                    @include("nomadelfia.templates.persona", ["persona" => $donna])
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end nomadelfi effettivi card -->
                    </div>
                    <!-- end accordion -->
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
