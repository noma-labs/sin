@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Sacerdoti"])

    <div class="row justify-content-md-center">
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header">
                    <h5>
                        Sacerdoti
                        <span class="badge bg-primary">
                            {{ count($sacerdoti) }}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 font-weight-bold">Nominativo</p>
                        <p class="col-md-3 font-weight-bold">Data inizio</p>
                        <p class="col-md-3 font-weight-bold">Durata</p>
                        <p class="col-md-3 font-weight-bold">Operazioni</p>
                    </div>

                    @forelse ($sacerdoti as $sacerdote)
                        <div class="row">
                            <p class="col-md-3">
                                @include("nomadelfia.templates.persona", ["persona" => $sacerdote])
                            </p>
                            <p class="col-md-3">
                                {{ $sacerdote->data_inizio }}
                            </p>
                            <p class="col-md-3">
                                @diffHumans($sacerdote->data_inizio)
                            </p>
                            <div class="col-md-3"></div>
                        </div>
                    @empty
                        <p class="text-danger">Nessun sacerdote</p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
            <!--end card -->
        </div>
    </div>
@endsection
