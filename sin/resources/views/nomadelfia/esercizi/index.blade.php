@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Esercizi Spirituali'])

@foreach(collect($esercizi)->chunk(3) as $chunk)
 <div class="row">
    @foreach ($chunk as $esercizio)
      <div class="col-md-4 my-1">
          <div id="accordion">
            <div class="card">
              <div class="card-header" id="heading{{$esercizio->id}}">
                <h5 class="mb-0">
                  <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{$esercizio->id}}" aria-expanded="false" aria-controls="collapse{{$esercizio->id}}">
                      {{$esercizio->turno}}  
                  <span class="badge badge-primary badge-pill"> {{$esercizio->personeOk()->total }}</span> 
                  </button>
                </h5>
              </div>
              <div id="collapse{{$esercizio->id}}" class="collapse" aria-labelledby="heading{{$esercizio->id}}" data-parent="#accordion">
                <div class="card-body">
                  <p> Responsabile:</p> 
                    @if ($esercizio->responsabile)
                      <span class="text-bold">{{$esercizio->responsabile->nominativo}} </span> 
                  @else
                      <span class="text-danger">Responsabile non assegnato</span> 
                  @endif
                  <a class="btn btn-primary" href="{{ route('nomadelfia.esercizi.dettaglio', $esercizio->id)}}">Modifica</a>
                </div>    
              </div>
            </div>
          </div>
      </div>      
     @endforeach
 </div> 
@endforeach


 <my-modal modal-title="Stampa Elechi" button-title="Stampa Es.Spirituali" button-style="btn-success my-2">
      <template slot="modal-body-slot">
      <form class="form" method="get"  id="formStampa" action="{{ route('nomadelfia.esercizi.stampa') }}" >      
       <h5>Seleziona gli elenchi da stampare:</h5>
       <div class="form-check">
          <input class="form-check-input" type="checkbox" value="maggMin" id="defaultCheck1"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck1">
            Popolazione Maggiorenni, Minorenni
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="effePostOspFig" id="defaultCheck1"   name="elenchi[]" checked>
          <label class="form-check-label" for="defaultCheck1">
            Effettivi, Postulanti, Ospiti, Figli
          </label>
        </div>
      
        </form>
      </template> 
      <template slot="modal-button">
        <button class="btn btn-success" form="formStampa">Salva</button>
      </template>
    </my-modal> 
@endsection
