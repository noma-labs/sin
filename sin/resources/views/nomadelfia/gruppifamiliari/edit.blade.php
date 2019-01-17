@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Modifica Gruppo familiare'])

{{$gruppo->nome}}


<div>
    <label class="font-weight-bold"> Capogruppo: </label>
    @if ($gruppo->capogruppoAttuale())
    <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$gruppo->capogruppoAttuale()->id])}}"> {{$gruppo->capogruppoAttuale()->nominativo}}</a>
    @else
    <span class="text-danger">Capogruppo non assegnato</span> 
    @endif
</div>
 

<div class="row">

    <div class="col-md-4">
        <p class="text-danger">Famiglie:</p> 

    @foreach($gruppo->persone as $persona)
        @if($persona->isSingle() or $persona->isCapofamiglia())
        <div class="card my-2">
            <div class="card-body">
                <div class="font-weight-bold">
                    <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a>
                    @if ($persona->famigliaAttuale()->moglie())  
                    <p>
                        <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->famigliaAttuale()->moglie()->id])}}"> {{$persona->famigliaAttuale()->moglie()->nominativo}}</a>
                    </p>
                    @endif
                </div>
                <ul>
                @foreach($persona->famigliaAttuale()->figliAttuali as $figlio)
                <li> @year($figlio->data_nascita)  
                    <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$figlio->id])}}"> {{$figlio->nominativo}}</a>
                </li>
                @endforeach
                </ul>
            
                <my-modal modal-title="Sposta in un nuovo gruppo familiare" button-title="Sposta Famiglia">
                    <template slot="modal-body-slot">
                    <form class="form" method="POST" id="formGruppo" action="{{ route('nomadelfia.persone.gruppo.modifica', ['idPersona' =>$persona->id]) }}" >      
                        {{ csrf_field() }}
                        <div class="form-group row">
                        <label for="example-text-input" class="col-4 col-form-label">Nuovo gruppo</label>
                            <div class="col-8">
                            <select class="form-control" name="nuovogruppo">
                            <option value="" selected>---scegli gruppo---</option>
                                @foreach (App\Nomadelfia\Models\GruppoFamiliare::all() as $gruppo)
                                    @if($gruppo->id != $persona->gruppofamiliareAttuale()->id)
                                    <option value="{{ $gruppo->id }}">{{ $gruppo->nome }}</option>
                                    @endif
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                        <label for="example-text-input" class="col-4 col-form-label">Data cambio gruppo:</label>
                            <div class="col-8">
                            <input type="date" class="form-control" name="datacambiogruppo" placeholder="Data cambio gruppo" >
                            </div>
                        </div>
                        <div class="form-group row">
                        <label for="example-text-input" class="col-4 col-form-label">Verranno spostati anche i seguenti componenti della famiglia</label>
                            <div class="col-8">
                            <ul>
                            @foreach($persona->famigliaAttuale()->componentiAttuali as $componente)
                                <li>{{$componente->nominativo}}</li>
                                @endforeach
                            </ul>
                            </div>
                        </div>
                    </form>
                    </template> 
                    <template slot="modal-button">
                        <button class="btn btn-danger" form="formGruppo">Salva</button>
                    </template>
                </my-modal>
            </div>
        </div>

        @else
        <!-- <p class="font-weight-bold">
          <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->id])}}">{{$persona->nominativo}}</a>
        </p> -->
        @endif
     @endforeach
    </div>
       
 </div>

    
@endsection


