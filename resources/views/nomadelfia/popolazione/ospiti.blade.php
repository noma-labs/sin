@extends("nomadelfia.index")

@section("archivio")
    @include("partials.header", ["title" => "Gestione Ospiti"])

    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <div class="card my-2">
                <div class="card-body">
                    <h3>
                        Ospiti
                        <span class="badge badge-primary">
                            {{ $ospiti->total }}
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
                        <span class="badge badge-primary">
                            {{ count($ospiti->uomini) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-4 font-weight-bold">Nominativo</p>
                        <p class="col-md-4 font-weight-bold">Data inizio</p>
                        <p class="col-md-4 font-weight-bold">Durata</p>
                    </div>

                    @forelse ($ospiti->uomini as $persona)
                        <div class="row">
                            <p class="col-md-4">
                                @include("nomadelfia.templates.persona", ["persona" => $persona])
                            </p>
                            <p class="col-md-4">{{ $persona->data_inizio }}</p>
                            <p class="col-md-4">
                                @diffHumans($persona->data_inizio)
                            </p>
                        </div>
                    @empty
                        <p class="text-danger">Nessun ospite</p>
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
                        <span class="badge badge-primary">
                            {{ count($ospiti->donne) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-4 font-weight-bold">Nominativo</p>
                        <p class="col-md-4 font-weight-bold">Data inizio</p>
                        <p class="col-md-4 font-weight-bold">Durata</p>
                    </div>

                    @forelse ($ospiti->donne as $persona)
                        <div class="row">
                            <p class="col-md-4">
                                @include("nomadelfia.templates.persona", ["persona" => $persona])
                            </p>
                            <p class="col-md-4">{{ $persona->data_inizio }}</p>
                            <p class="col-md-4">
                                @diffHumans($persona->data_inizio)
                            </p>
                        </div>
                    @empty
                        <p class="text-danger">Nessun ospite</p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
        </div>
    </div>
@endsection
