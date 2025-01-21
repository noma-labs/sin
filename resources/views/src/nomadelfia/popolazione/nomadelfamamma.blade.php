@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Nomadelfi mamme"])

    <div class="row justify-content-md-center">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Nomadelfe Mamme
                        <span class="badge text-bg-primary">
                            {{ count($nmamma) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 fw-bold">Nominativo</p>
                        <p class="col-md-3 fw-bold">Data inizio</p>
                        <p class="col-md-3 fw-bold">Durata</p>
                        <p class="col-md-3 fw-bold">Operazioni</p>
                    </div>

                    @forelse ($nmamma as $mamma)
                        <div class="row">
                            <p class="col-md-3">
                                @include("nomadelfia.templates.persona", ["persona" => $mamma])
                            </p>
                            <p class="col-md-3">{{ $mamma->data_inizio }}</p>
                            <p class="col-md-3">
                                @diffHumans($mamma->data_inizio)
                            </p>
                            <div class="col-md-3"></div>
                        </div>
                    @empty
                        <p class="text-danger">Nessuna Nomadelfa mamma</p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
        </div>
    </div>
@endsection
