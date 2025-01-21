@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Maggiorenni"])

    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <div class="card my-2">
                <div class="card-body">
                    <h3>
                        Maggiorenni
                        <span class="badge text-bg-primary">
                            {{ $maggiorenni->total }}
                        </span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Uomini
                        <span class="badge text-bg-primary">
                            {{ count($maggiorenni->uomini) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-4 fw-bold">Nominativo</p>
                        <p class="col-md-4 fw-bold">Data Entrata</p>
                        <p class="col-md-4 fw-bold">Durata</p>
                    </div>

                    @forelse ($maggiorenni->uomini as $persona)
                        <div class="row">
                            <p class="col-md-4">
                                @include("nomadelfia.templates.persona", ["persona" => $persona])
                            </p>
                            <p class="col-md-4">
                                {{ $persona->data_entrata }}
                            </p>
                            <p class="col-md-4">
                                @diffHumans($persona->data_entrata)
                            </p>
                        </div>
                    @empty
                        <p class="text-danger">Nessun maggiorenne</p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Donne
                        <span class="badge text-bg-primary">
                            {{ count($maggiorenni->donne) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 fw-bold">Nominativo</p>
                        <p class="col-md-3 fw-bold">Data Entrata</p>
                        <p class="col-md-3 fw-bold">Durata</p>
                        <p class="col-md-3 fw-bold">Operazioni</p>
                    </div>

                    @forelse ($maggiorenni->donne as $persona)
                        <div class="row">
                            <p class="col-md-3">
                                @include("nomadelfia.templates.persona", ["persona" => $persona])
                            </p>
                            <p class="col-md-3">
                                {{ $persona->data_entrata }}
                            </p>
                            <p class="col-md-3">
                                @diffHumans($persona->data_entrata)
                            </p>
                            <div class="col-md-3"></div>
                        </div>
                    @empty
                        <p class="text-danger">Nessun maggiorenne</p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
        </div>
    </div>
@endsection
