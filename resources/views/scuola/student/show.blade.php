@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Scheda alunno"])

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">Anagrafica</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Nome:</label>
                                <div class="col-sm-8">
                                    <span>{{ $student->nome }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Cognome:</label>
                                <div class="col-sm-8">
                                    <span>{{ $student->cognome }}</span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Codice Fiscale:
                                </label>
                                <div class="col-sm-8">
                                    <span>
                                        @if ($student->cf)
                                            {{ $student->cf }}
                                        @else
                                            <p class="text-danger">
                                                Nessun codice fiscale
                                            </p>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Data Nascita:
                                </label>
                                <div class="col-sm-8">
                                    <span>
                                        {{ $student->data_nascita }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">
                                    Luogo Nascita:
                                </label>
                                <div class="col-sm-8">
                                    <span>
                                        {{ $student->provincia_nascita }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <label class="col-sm-4 fw-bold">Sesso:</label>
                                <div class="col-sm-8">
                                    <span>{{ $student->sesso }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">Classi frequentate</div>
                <div class="card-body">
                    <ul>
                        @foreach ($student->classe as $classe)
                            <li>
                                <a
                                    href="{{ route("scuola.anno.show", $classe->anno->id) }}"
                                >
                                    {{ $classe->anno->scolastico }}
                                </a>
                                :
                                <a
                                    href="{{ route("scuola.classi.show", $classe->id) }}"
                                >
                                    {{ $classe->tipo->ciclo }}
                                    {{ $classe->tipo->nome }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">Elaborati</div>
                <div class="card-body">
                    <ul>
                        @foreach ($student->elaborato as $elaborato)
                            <li>
                                <a
                                    href="{{ route("scuola.elaborati.show", $elaborato->id) }}"
                                >
                                    {{ $elaborato->anno_scolastico }}:
                                    {{ $elaborato->titolo }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
