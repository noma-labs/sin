@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Gestione Gruppo familiare'])

<div class="row justify-content-md-center">
    <div class="col-md-10">
      <div class="card my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-6">Gruppo Familiare:</label>
                        <div class="col-sm-6">
                        {{$gruppo->nome}}
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6">Capogruppo:</label>
                        <div class="col-sm-6">
                            @if ($gruppo->capogruppoAttuale())
                                <a href="{{route('nomadelfia.persone.dettaglio',['idPersona'=>$gruppo->capogruppoAttuale()->id])}}"> {{$gruppo->capogruppoAttuale()->nominativo}}</a>
                            @else
                                <span class="text-danger">Capogruppo non assegnato</span> 
                            @endif
                        </div>
                    </div>
                </div> <!--end col dati gruppo -->
                <div class="col-md-6">
                    @foreach($countPosizioniFamiglia as $posizione)
                        {{$posizione->posizione_famiglia}}
                        <span class="badge badge-info"> {{$posizione->total}}</span>
                    @endforeach
                </div> <!--end col dati statistici -->
            </div>  <!--end row -->
           
        </div>
       </div>  <!--end card -->
    </div> 
</div>


<div class="row justify-content-around my-2">
    <div class="col-md-4">
        <h5>Famiglie  <span class="badge badge-info ">{{$gruppo->famiglieAttuale->count()}}</span></h5>
        <div class="accordion" id="accordionExample">
            @foreach($gruppo->famiglieAttuale as $famiglia)
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
                                        <input type="date" class="form-control" name="data_cambiogruppo" placeholder="Data cambio gruppo" >
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-4 col-form-label">Verranno spostati anche i seguenti componenti della famiglia</label>
                                        <div class="col-8">
                                            <ul>
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
                     </div>  <!-- end card footer  -->
                </div>
            </div> <!-- end card  -->
            @endforeach
        </div> <!-- end accordion  -->
    </div>

    <div class="col-md-4">
        <h5>Persone <span class="badge badge-info ">{{$gruppo->personeAttuale->count()}}</span> </h5>
        <div class="card">
            <ul>
            @foreach($gruppo->personeAttuale as $persona)
                 <li> @include("nomadelfia.templates.persona", ['persona' => $persona])</li>
            @endforeach
            </ul>
        </div>
    </div>
 </div>

    
@endsection


