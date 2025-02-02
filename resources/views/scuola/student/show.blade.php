@extends("scuola.student.layout")

@section("content")
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
                        <label class="form-label col-sm-4 fw-bold">Cognome:</label>
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
                        <label class="form-label col-sm-4 fw-bold">Sesso:</label>
                        <div class="col-sm-8">
                            <span>{{ $student->sesso }}</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@endsection
