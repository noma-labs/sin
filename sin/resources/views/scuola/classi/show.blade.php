@extends('nomadelfia.index')

@section('archivio')
    @include('partials.header', ['title' => 'Gestione Classe'])

    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <div class="card border-dark my-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <label class="col-sm-6 font-weight-bold">Classe:</label>
                                <div class="col-sm-6">
                                    {{$classe->tipo->nome}}
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-6 font-weight-bold">Responsabile:</label>
                                <div class="col-sm-6">
                                    <span class="text-danger">Responsabile non assegnato</span>
                                </div>
                            </div>
                        </div> <!--end col dati gruppo -->
                        <div class="col-md-8">
                            @include("scuola.templates.aggiungiAlunno",["classe"=>$classe])
                        </div> <!--end col -->
                    </div>  <!--end row -->
                </div> <!--end card body -->
            </div>  <!--end card -->

        </div>
    </div>

    <div class="row justify-content-md-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Alunni <span class="badge badge-primary">{{$alunni->count()}}</span></h4>
                    <ul class="list-group list-group-flush">
                        @foreach($alunni->get() as $alunno)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="font-weight-bold mt-2">
                                            @year($alunno->data_nascita)
                                            @include("nomadelfia.templates.persona", ['persona' => $alunno])
                                            (@diffYears($alunno->data_nascita) anni)
                                            @liveRome($alunno)
                                                <span class="badge badge-warning">Roma</span>
                                            @endliveRome
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-2">
                                        @include('scuola.templates.rimuoviAlunno', ['classe'=> $classe, 'alunno' => $alunno] )
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
                <div class="card-body">
                    <h4 class="card-title">Coordinatori</h4>
                    <ul class="list-group list-group-flush">

                        <ul>
                </div>
            </div>
        </div>


    </div>
@endsection