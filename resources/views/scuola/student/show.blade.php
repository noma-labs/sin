@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Alunno: " . $student->nome . " " . $student->cognome])
    <ul
        class="nav nav-pills pb-3 flex-column flex-sm-row justify-content-center"
    >
        <a
            class="nav-link active"
            aria-current="page"
            href="{{ route("scuola.student.show", $student->id) }}"
        >
            Anagrafica
        </a>
        <a
            class="nav-link"
            href="{{ route("scuola.student.works.show", $student->id) }}"
        >
            Elaborati
        </a>
        <a
            class="nav-link"
            href="{{ route("scuola.student.classes.show", $student->id) }}"
        >
            Classi
        </a>
    </ul>

    <div class="card mb-3">
        <div class="card-header">Anagrafica</div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="row">
                        <label class="form-label col-sm-4 fw-bold">Nome:</label>
                        <div class="col-sm-8">
                            <span>{{ $student->nome }}</span>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <label class="form-label col-sm-4 fw-bold">
                            Cognome:
                        </label>
                        <div class="col-sm-8">
                            <span>{{ $student->cognome }}</span>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <label class="form-label col-sm-4 fw-bold">
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
                        <label class="form-label col-sm-4 fw-bold">
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
                        <label class="form-label col-sm-4 fw-bold">
                            Sesso:
                        </label>
                        <div class="col-sm-8">
                            <span>{{ $student->sesso }}</span>
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <label class="form-label col-sm-4 fw-bold">
                            Famiglia (da anagrafe Enrico):
                        </label>
                        <div class="col-sm-8 alert alert-warning">
                            @if($famigliaEnrico)
                            <span>{{ $famigliaEnrico  }}</span>
                            @else
                            <span> Non presente </span>
                            @endif
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@endsection
