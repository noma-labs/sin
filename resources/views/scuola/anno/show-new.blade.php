@extends("scuola.index")

@section("title", "Gestione Scuola")

@section("content")
    @include("partials.header", ["title" => "Anno scolastico " . $anno->as])

    <div class="mb-3">
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
                            {{ $anno->descrizione }}
                        </div>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        <p>Data inizio</p>
                        <span class="badge bg-secondary">
                            {{ $anno->data_inizio->format("Y-m-d") }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        Studenti
                        <span class="badge bg-primary rounded-pill">
                            {{ $anno->totalStudents }}
                        </span>
                    </li>
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        Operazioni
                        <div>
                            @include("scuola.templates.aggiungiNoteAnno", ["anno" => $anno])
                            @include("scuola.templates.cloneAnnoDaPrecedente", ["anno" => $anno])
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-5 g-3" id="accordion">
        <div class="col">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapsePrescuola"
                            aria-expanded="true"
                            aria-controls="collapsePrescuola"
                        >
                            Prescuola
                            <span class="badge bg-secondary rounded-pill">
                                {{ $anno->prescuola->alunniCount }}
                            </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapsePrescuola"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->prescuola->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge bg-secondary rounded-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita)
                                            @include("scuola.templates.student", ["persona" => $alunno])
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseElementari"
                            aria-expanded="true"
                            aria-controls="collapseElementari"
                        >
                            Elementari
                            <span class="badge bg-secondary rounded-pill">
                                {{ $anno->elementari->alunniCount }}
                            </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseElementari"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->elementari->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge bg-secondary rounded-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita)
                                            @include("scuola.templates.student", ["persona" => $alunno])
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseMedie"
                            aria-expanded="true"
                            aria-controls="collapseMedie"
                        >
                            Medie
                            <span class="badge bg-secondary rounded-pill">
                                {{ $anno->medie->alunniCount }}
                            </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseMedie"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->medie->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge bg-secondary rounded-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita)
                                            @include("scuola.templates.student", ["persona" => $alunno])
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseSuperiori"
                            aria-expanded="true"
                            aria-controls="collapseSuperiori"
                        >
                            Superiori
                            <span class="badge bg-secondary rounded-pill">
                                {{ $anno->superiori->alunniCount }}
                            </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseSuperiori"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->superiori->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge bg-secondary rounded-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita)
                                            @include("scuola.templates.student", ["persona" => $alunno])
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                        <button
                            class="btn btn-link"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseUniversità"
                            aria-expanded="true"
                            aria-controls="collapseUniversità"
                        >
                            Università
                            <span class="badge bg-secondary rounded-pill">
                                {{ $anno->universita->alunniCount }}
                            </span>
                        </button>
                    </h5>
                </div>

                <div
                    id="collapseUniversità"
                    class="collapse show"
                    aria-labelledby="headingOne"
                    data-parent="#accordion"
                >
                    <div class="card-body">
                        @foreach ($anno->universita->classi as $classe)
                            <div>
                                {{ $classe->nome }}
                                <span class="badge bg-secondary rounded-pill">
                                    {{ count($classe->alunni) }}
                                </span>

                                <ul>
                                    @foreach ($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita)
                                            @include("scuola.templates.student", ["persona" => $alunno])
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
