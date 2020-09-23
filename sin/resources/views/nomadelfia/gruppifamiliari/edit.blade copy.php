@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Gestione Gruppo familiare'])

<div class="row justify-content-md-center">
    <div class="col-md-10">
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

   <!-- @foreach($gruppo->personeAttuale as $persona)
                      @if($persona->isSingle())
                        <p><a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a></p>
                       @elseif($persona->isCapofamiglia())
                        <div class="font-weight-bold mt-3">
                          <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->id])}}"> {{$persona->nominativo}}</a>
                        </div>
                        @if($persona->famigliaAttuale())
                          <div class="font-weight-bold">
                            @if ($persona->famigliaAttuale()->moglie())  
                              <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->famigliaAttuale()->moglie()->id])}}"> {{$persona->famigliaAttuale()->moglie()->nominativo}}</a>
                            @endif
                          </div>
                          <ul>
                            @foreach($persona->famigliaAttuale()->figliAttuali as $figlio)
                            <li> @year($figlio->data_nascita)  
                              <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$figlio->id])}}"> {{$figlio->nominativo}}</a>
                            </li>
                            @endforeach
                          </ul>
                         @endif
                      @endif
                    @endforeach -->


<div class="row justify-content-around my-2">
    <div class="col-md-4">
    <div class="card">
    <div class="card-body">
        <h5 class="card-title">Persone divise per famiglie <span class="badge badge-primary">{{$gruppo->personeAttualeViaFamiglie()->count()}}</span></h5>
        <ul class="list-group list-group-flush">
                foreach($capoFamiglie as $cp)
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
                @foreach($single as $sg)
                <li class="list-group-item">
                        <div class="row">
                                <div class="col-md-6">
                                        <div class="font-weight-bold mt-2">
                                                {{$sg->nome_famiglia}}
                                            </div> 
                                </div>
                        </div>
                
                </li>
                @endforeach
        @foreach($famiglie as $famiglia)
        <li class="list-group-item">
                <div class="row">
                        <div class="col-md-6">
                                <div class="font-weight-bold mt-2">
                                        {{$famiglia->nome_famiglia}}{{$famiglia->nominativo}}
                                    </div> 
                        </div>
                </div>
        
        </li>
        @endforeach
        @foreach($gruppo->famiglieAttuale as $famiglia)
            <li class="list-group-item">
                <div class="row">
                    <div class="col-md-6">
                        @if($famiglia->single())
                            <div class="font-weight-bold mt-2">
                                @include('nomadelfia.templates.persona',['persona'=>$famiglia->single()]) 
                            </div> 
                            @elseif($famiglia->Capofamiglia())
                                <div class="font-weight-bold mt-3">
                                    @include('nomadelfia.templates.persona',['persona'=>$famiglia->Capofamiglia()])
                                </div>
                                @if ($famiglia->moglie())  
                                <div class="font-weight-bold">
                                    @include('nomadelfia.templates.persona',['persona'=>$famiglia->moglie()])
                                </div>
                                @endif
                                <ul>
                                @foreach($famiglia->figliAttuali as $figlio)
                                <li> @year($figlio->data_nascita)  
                                    <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$figlio->id])}}"> 
                                    {{$figlio->nominativo}}</a>
                                </li>
                                @endforeach
                                </ul>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <my-modal modal-title="Sposta Famiglia in un nuovo gruppo familiare" button-title="Sposta Famiglia" >
                                <template slot="modal-body-slot">
                                    <form class="form" method="POST" id="formFamigliaGruppo{{$famiglia->id}}" action="{{ route('nomadelfia.famiglie.gruppo.assegna', ['id' =>$famiglia->id]) }}" >      
                                    {{ csrf_field() }}
                                        <div class="form-group row">
                                        <label for="example-text-input" class="col-4 col-form-label">Nuovo gruppo</label>
                                            <div class="col-8">
                                            <select class="form-control" name="gruppo_id">
                                            <option value="" selected>---scegli gruppo---</option>
                                                @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppofamiliare)
                                                    @if($gruppofamiliare->id != $famiglia->gruppofamiliareAttuale()->id)
                                                    <option value="{{ $gruppofamiliare->id }}">{{ $gruppofamiliare->nome }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                        <label for="example-text-input" class="col-4 col-form-label">Data cambio gruppo:</label>
                                            <div class="col-8">
                                            <date-picker :bootstrap-styling="true"  format="yyyy-MM-dd" name="data_cambiogruppo"></date-picker>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col">
                                            <div class="text-justify"> Le seguenti persone saranno spostate nel gruppo familiare selezionato:</p>
                                                <ul >
                                                @foreach($famiglia->componentiAttuali as $componente)
                                                    <li>{{$componente->nominativo}}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </form>
                                </template> 
                                <template slot="modal-button">
                                    <button class="btn btn-danger" form="formFamigliaGruppo{{$famiglia->id}}" type="submit">Salva</button>
                                </template>
                        </my-modal>
                        <my-modal modal-title="Conferma eliminazione famiglia" button-title="Elimina Famiglia" button-style="btn-danger">
                                <template slot="modal-body-slot">
                                    Vuoi davvero eliminare la famiglia {{$famiglia->nome_famiglia}} dal gruppo {{$gruppo->nome}} ??
                                   
                                </template> 
                                <template slot="modal-button">
                                    <form class="form" method="post"action="{{ route('nomadelfia.famiglie.gruppo.elimina', ['id' =>$famiglia->id, 'idGruppo'=>$gruppo->id]) }}" >  
                                        {!! method_field('delete') !!}    
                                        {{ csrf_field() }}
                                        <button class="btn btn-danger" type="submit">Elimina</button>
                                    </form>
                                </template>
                        </my-modal>
                    </div>
                </div>
            </li>
        @endforeach
        <ul>
    </div> 
    </div> 




         <!--<div class="accordion" id="accordionExample">
            <div class="card my-1">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$famiglia->id}}" aria-expanded="true" aria-controls="collapse{{$famiglia->id}}">
                        {{$famiglia->nome_famiglia}} 
                        <span class="badge badge-info ">{{$famiglia->componentiAttuali->count()}}</span>
                        </button>
                    </h2>
                </div>
                <div id="collapse{{$famiglia->id}}" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                         @include("nomadelfia.templates.famigliaAttuale", ['famiglia' => $famiglia])
                    </div>
                    <div class="card-footer text-muted">
                        <my-modal modal-title="Sposta Famiglia in un nuovo gruppo familiare" button-title="Sposta Famiglia">
                            <template slot="modal-body-slot">
                                <form class="form" method="POST" id="formFamigliaGruppo{{$famiglia->id}}" action="{{ route('nomadelfia.famiglie.gruppo.assegna', ['id' =>$famiglia->id]) }}" >      
                                {{ csrf_field() }}
                                    <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label">Nuovo gruppo</label>
                                        <div class="col-8">
                                        <select class="form-control" name="gruppo_id">
                                        <option value="" selected>---scegli gruppo---</option>
                                            @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppofamiliare)
                                                @if($gruppofamiliare->id != $famiglia->gruppofamiliareAttuale()->id)
                                                <option value="{{ $gruppofamiliare->id }}">{{ $gruppofamiliare->nome }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label">Data cambio gruppo:</label>
                                        <div class="col-8">
                                        <date-picker :bootstrap-styling="true"  format="yyyy-MM-dd" name="data_cambiogruppo"></date-picker>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col">
                                        <div class="text-justify"> Le seguenti persone saranno spostate nel gruppo familiare selezionato:</p>
                                            <ul >
                                            @foreach($famiglia->componentiAttuali as $componente)
                                                <li>{{$componente->nominativo}}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </form>
                            </template> 
                            <template slot="modal-button">
                                <button class="btn btn-danger" form="formFamigliaGruppo{{$famiglia->id}}" type="submit">Salva</button>
                            </template>
                        
                        </my-modal>
                     </div>  <! -- end card footer  -- >
                </div>
            </div>  
        </div> <! -- end accordion  -->
    </div>

    <div class="col-md-4">
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


