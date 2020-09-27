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
                        <label class="col-sm-6 font-weight-bold">Numero componenti:</label>
                        <div class="col-sm-3">
                        <span class="badge badge-info ">{{$gruppo->personeAttuale->count()}}</span>
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
                <h4 class="card-title">Persone divise per famiglie</h4>
                <ul class="list-group list-group-flush">
                  
                    @foreach($single as $famiglia_single_id => $componente)
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="font-weight-bold mt-2">
                                    @if ($famiglia_single_id === "")
                                        !!! Senza famiglia !!!
                                    @else
                                        <a href="{{route('nomadelfia.famiglia.dettaglio',['idFamiglia'=>$famiglia_single_id])}}"> 
                                                {{$componente->nominativo}}
                                        </a>
                                    @endif
                                </div> 
                            </div>
                        </div>
                    </li>
                    @endforeach
                    @foreach($famiglie as $famiglia_id => $componenti)
                    <li class="list-group-item">
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="font-weight-bold mt-2">
                                         @if ($famiglia_id === "")
                                           !!! Senza famiglia !!!
                                        @else
                                         Fam. 
                                         <a href="{{route('nomadelfia.famiglia.dettaglio',['idFamiglia'=>$famiglia_id])}}"> 
                                                {{App\Nomadelfia\Models\Famiglia::find($famiglia_id)->nome_famiglia}} 
                                         </a>({{count($componenti)}})
                                        @endif
                                    </div> 
                                    <ul>
                                    @foreach($componenti as $componente)
                                    <li> @year($componente->data_nascita)  
                                        <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$componente->persona_id])}}"> 
                                        {{$componente->nominativo}}</a>
                                    </li>
                                    @endforeach
                                    </ul>
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
         <h4 class="card-title">Persone <span class="badge badge-primary">{{$gruppo->personeAttuale->count()}}</span></h4>
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


