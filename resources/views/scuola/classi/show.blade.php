@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Classe"])

    <div class="card mb-3">
        <div class="card-header">
            Classe {{ $classe->tipo->nome }}
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <p>A.S.</p>
                    <a
                        href="{{ route("scuola.anno.show", $anno->id) }}"
                    >
                        {{ $anno->scolastico }}
                    </a>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <p>Classe:</p>
                    <div>
                        {{ $classe->tipo->nome }}
                        @include("scuola.templates.modificaTipoClasse", ["classe" => $classe])
                    </div>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    <p>Note</p>
                    <div>
                        {{ $classe->note }}
                        @include("scuola.templates.aggiungiNoteClasse", ["classe" => $classe])
                    </div>
                </li>
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    Elaborato
                    <a
                        class="btn btn-warning"
                        href="{{ route("scuola.classi.elaborato.create", $classe->id) }}"
                    >
                        Aggiungi Elaborato
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row justify-content-md-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            Alunni
                            <span class="badge bg-primary">
                                {{ $alunni->count() }}
                            </span>
                        </div>
                        <div class="col-2">
                            @include("scuola.templates.aggiungiAlunno", ["classe" => $classe])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($alunni->get() as $alunno)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fw-bold mt-2">
                                            @year($alunno->data_nascita)
                                            @include("nomadelfia.templates.persona", ["persona" => $alunno])
                                            (
                                            @diffYears($alunno->data_nascita)
                                            anni)
                                            @liveRome($alunno)
                                                <span class="badge bg-warning">
                                                    Roma
                                                </span>
                                            @endliveRome
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-2">
                                        @include("scuola.templates.rimuoviAlunno", ["classe" => $classe, "alunno" => $alunno])
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            Coordinatori
                            <span class="badge bg-primary">
                                {{ $coords->count() }}
                            </span>
                        </div>
                        <div class="col-4">
                            @include("scuola.templates.aggiungiCoordinatore", ["classe" => $classe])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($coords->get() as $coord)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="fw-bold">
                                            @include("nomadelfia.templates.persona", ["persona" => $coord])
                                            <span>
                                                ({{ $coord->pivot->tipo }})
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-2">
                                        @include("scuola.templates.rimuoviCoordinatore", ["classe" => $classe, "alunno" => $coord])
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
