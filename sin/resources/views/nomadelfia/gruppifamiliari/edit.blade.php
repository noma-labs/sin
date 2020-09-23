@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Gestione Gruppo familiare'])

<div class="row justify-content-md-center">
    <div class="col-md-12">
      <div class="card border-dark my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Gruppo Familiare:</label>
                        <div class="col-sm-6">
                        {{$gruppo->nome}}
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Capogruppo:</label>
                        <div class="col-sm-6">
                            @if ($gruppo->capogruppoAttuale())
                                <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$gruppo->capogruppoAttuale()->id])}}"> {{$gruppo->capogruppoAttuale()->nominativo}}</a>
                            @else
                                <span class="text-danger">Capogruppo non assegnato</span> 
                            @endif
                        </div>
                    </div>
                </div> <!--end col dati gruppo -->
                <div class="col-md-8">
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Numero famiglie:</label>
                        <div class="col-sm-3">
                        <span class="badge badge-info ">{{$gruppo->famiglieAttuale->count()}}</span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Numero persone per posizione:</label>
                        <div class="col-sm-6">
                        @foreach($countPosizioniFamiglia as $posizione)
                            <span class="text-lowercase">{{$posizione->posizione_famiglia}}</span>
                            <span class="badge badge-info"> {{$posizione->total}}</span>
                        @endforeach

                        </div>
                    </div>
                   
                </div> <!--end col dati statistici -->
            </div>  <!--end row -->           
         </div> <!--end card body -->
       </div>  <!--end card -->
    </div> 
</div>

<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Persone divise per famiglie <span class="badge badge-primary">{{$gruppo->personeAttualeViaFamiglie()->count()}}</span></h5>
                <ul class="list-group list-group-flush">
                  
                    @foreach($single as $cp)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="font-weight-bold mt-2">
                                        {{$cp->nome_famiglia}}
                                </div> 
                            </div>
                        </div>
                    </li>
                    @endforeach
                    @foreach($capoFamiglie as $nomefamiglia => $famiglia)
                    <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="font-weight-bold mt-2">
                                            {{$nomefamiglia}} ({{count($capoFamiglie)}})
                                    </div> 
                                    <ul>
                                    @foreach($famiglia as $componente)
                                    <li> @year($componente->data_nascita)  
                                        <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$componente->id])}}"> 
                                        {{$componente->nominativo}}</a>
                                    </li>
                                    @endforeach
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                   
                                </div>
                            </div>
                        </li>
                    @endforeach
                <ul>
            </div> 
        </div> 
    </div>

    <div class="col-md-6">
        <div class="card">
         <div class="card-body">
         <h5 class="card-title">Persone singole <span class="badge badge-primary">{{$gruppo->personeAttuale->count()}}</span></h5>
            <ul class="list-group list-group-flush">
            @foreach($gruppo->personeAttuale as $persona)
                 <li class="list-group-item"> @year($persona->data_nascita)
                        @include("nomadelfia.templates.persona", ['persona' => $persona]) 
                        (@diffYears($persona->data_nascita) anni)</li>
            @endforeach
            </ul>
        </div>
        </div>
    </div>
 </div>    
@endsection


