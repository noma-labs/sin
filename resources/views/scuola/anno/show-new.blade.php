@extends("scuola.index")

@section("title", "Gestione Scuola")

@section("content")
    @include("partials.header", ["title" => "Anno scolastico " . $anno->as])

    <div class="row">
        <div class="col-md-12">
            <div class="card-deck">
                <div class="card">
                    <div class="card-header">Scuola A.S. {{ $anno->as }}</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                <p>Responsabile Scuola</p>
                                @if ($anno->responsabile)
                                    @include("nomadelfia.templates.persona", ["persona" => $anno->responsabile])
                                @else
                                        Non Assegnato
                                @endif
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                <p>Note</p>
                                <div>
                                    @include("scuola.templates.aggiungiNoteAnno", ["anno" => $anno])
                                    {{ $anno->descrizione }}
                                </div>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                <p>Data inizio</p>
                                <span class="badge badge-secondary">
                                    {{ $anno->data_inizio->format("2000-01-01") }}
                                </span>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-center"
                            >
                                Studenti
                                <span class="badge badge-primary badge-pill">
                                    {{ $anno->totalStudents }}
                                </span>
                            </li>
                            <li class="list-group-item">
                                <ul class="list-group list-group-flush">
                                    <li
                                        class="list-group-item d-flex justify-content-end align-items-center"
                                    >
                                        Prescuola
                                        <span
                                            class="badge badge-secondary badge-pill ml-2"
                                        >
                                            {{ $anno->prescuola->alunniCount }}
                                        </span>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3" id="accordion">
        <div class="col-md">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-toggle="collapse"
                            data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne"
                        >
                            Prescuola  <span class="badge badge-secondary badge-pill"> {{ $anno->prescuola->alunniCount }}  </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseOne"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->prescuola->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge badge-secondary badge-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            {{ $alunno }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-toggle="collapse"
                            data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne"
                        >
                            Elementari  <span class="badge badge-secondary badge-pill"> {{ $anno->elementari->alunniCount }}  </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseOne"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->elementari->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge badge-secondary badge-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            {{ $alunno }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-toggle="collapse"
                            data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne"
                        >
                            Medie  <span class="badge badge-secondary badge-pill"> {{ $anno->medie->alunniCount }}  </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseOne"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->medie->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge badge-secondary badge-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            {{ $alunno }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-toggle="collapse"
                            data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne"
                        >
                            Superiori  <span class="badge badge-secondary badge-pill"> {{ $anno->superiori->alunniCount }}  </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseOne"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->superiori->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge badge-secondary badge-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            {{ $alunno }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-toggle="collapse"
                            data-target="#collapseOne"
                            aria-expanded="true"
                            aria-controls="collapseOne"
                        >
                            Università  <span class="badge badge-secondary badge-pill"> {{ $anno->universita->alunniCount }}  </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseOne"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->universita->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge badge-secondary badge-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            {{ $alunno }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
