@extends('scuola.index')

@section('archivio')
    @include('partials.header', ['title' => 'Gestione Classe'])

    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <div class="card border-dark my-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            A.S.:<span class="font-weight-bold"><a href="{{ route('scuola.anno.show',$anno->id)}}">{{$anno->scolastico}}</a></span>
                        </div>
                        <div class="col-md-4">
                            Classe:<span class="font-weight-bold">  {{$classe->tipo->nome}}          </span>
                        </div> <!--end col -->
                        <div class="col-md-4">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-md-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            Alunni <span class="badge badge-primary">{{$alunni->count()}}</span>
                        </div>
                        <div class="col-4">
                         @include("scuola.templates.aggiungiAlunno",["classe"=>$classe])
                        </div>
                    </div>
                </div>
                <div class="card-body">
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
                <div class="card-header">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            Coordinatori <span class="badge badge-primary">{{$coords->count()}}</span>
                        </div>
                        <div class="col-4">
                            @include("scuola.templates.aggiungiCoordinatore",["classe"=>$classe])
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach($coords->get() as $coord)
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="font-weight-bold">
                                            @include("nomadelfia.templates.persona", ['persona' => $coord])
                                            <span> ({{$coord->pivot->tipo}})</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 offset-md-2">
                                        @include('scuola.templates.rimuoviCoordinatore', ['classe'=> $classe, 'alunno' => $coord] )
                                    </div>
                                </div>
                            </li>
                        @endforeach
                        <ul>
            </div>
    </div>
@endsection
