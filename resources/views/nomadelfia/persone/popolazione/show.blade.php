@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Entrate/Uscite: ".$persona->nominativo])


<div class="row justify-content-center">
    <div class="col-md-6">

        @if($persona->isPersonaInterna())
        <div class="card">
            <div class="card-header">
                Posizione attuale
            </div>
            <div class="card-body">
                <div class="row">
                    <p class="col-md-4 font-weight-bold"> Data Entrata</p>
                    <p class="col-md-2 font-weight-bold"> Tempo trascorso </p>
                    <p class="col-md-6 font-weight-bold"> Operazioni</p>
                </div>
                <div class="row">
                    <p class="col-md-4"> {{$attuale->data_entrata}}</p>
                    <div class="col-md-2">
                                                <span class="badge badge-info">
                        {{Carbon::parse($attuale->data_entrata)->diffForHumans(['short' => true])}}
                                                </span>
                    </div>
                    <div class="col-md-6">
                        @include("nomadelfia.templates.modificaDataEntrata",["persona"=>$persona,
                        "data_entrata"=>$attuale->data_entrata])
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card my-3">
            <div class="card-header">
                Storico Entrate/Uscite
            </div>
            <div class="card-body">
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-2 font-weight-bold"> Data entrata</p>
                        <p class="col-md-2 font-weight-bold"> Data uscita </p>
                        <p class="col-md-2 font-weight-bold"> Durata </p>
                        <p class="col-md-6 font-weight-bold"> Operazioni </p>
                    </div>

                    @forelse($storico as $storicoPopolazione)

                    <div class="row">
                        <p class="col-md-2">{{$storicoPopolazione->data_entrata }} </p>
                        <p class="col-md-2">{{$storicoPopolazione->data_uscita }} </p>

                        <div class="col-md-2">
            <span class="badge badge-info"> 
            {{Carbon::parse($storicoPopolazione->data_entrata)->diffForHumans(Carbon::parse($storicoPopolazione->data_uscita),['short' => true])}}</span>
                        </div>
                        <div class="col-md-6">
                            @include("nomadelfia.templates.modificaDataEntrata",["persona"=>$persona,
                            "data_entrata"=>$storicoPopolazione->data_entrata])

                            @include("nomadelfia.templates.modificaDataUscita",["persona"=>$persona,
                            "data_uscita"=>$storicoPopolazione->data_uscita])

                        </div>

                    </div>

                    @empty
                    <p class="text-danger">Nessuna posizione nello storico</p>
                    @endforelse
                </div>  <!--end card body-->
            </div> <!--end card -->

        </div> <!--end card -->
    </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
