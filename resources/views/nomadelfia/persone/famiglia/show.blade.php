@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Gestione Famiglie di " . $persona->nominativo])

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Famiglia Attuale</div>
                <div class="card-body">
                    @if ($attuale)
                        <div class="row">
                            <p class="col-md-3 font-weight-bold">
                                Nome Famigla
                            </p>
                            <p class="col-md-3 font-weight-bold">
                                Posizione Famiglia
                            </p>
                            <p class="col-md-3 font-weight-bold">Operazioni</p>
                        </div>
                        <div class="row">
                            <p class="col-md-3">
                                <a
                                    href="{{ route("nomadelfia.famiglia.dettaglio", ["id" => $attuale->id]) }}"
                                >
                                    {{ $attuale->nome_famiglia }}
                                </a>
                            </p>
                            <p class="col-md-3">
                                {{ $attuale->pivot->posizione_famiglia }}
                            </p>
                        </div>
                    @else
                        <div class="row">
                            <p class="text-danger">Nessuna famiglia</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card my-3">
                <div class="card-header">Storico famiglie</div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 font-weight-bold">Nome famiglia</p>
                        <p class="col-md-3 font-weight-bold">Posizione</p>
                    </div>

                    @forelse ($storico as $famigliaStorico)
                        <div class="row">
                            <p class="col-md-3">
                                {{ $famigliaStorico->nome_famiglia }}
                            </p>
                            <p class="col-md-3">
                                {{ $famigliaStorico->pivot->posizione_famiglia }}
                            </p>
                        </div>
                    @empty
                        <p class="text-danger">
                            Nessuna famiglia nello storico
                        </p>
                    @endforelse
                </div>
                <!--end card body-->
            </div>
        </div>
    </div>
@endsection
