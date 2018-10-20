@extends('patente.index')

@section('archivio')
<sin-header title="Modifica Patente"> </sin-header>
<patente-modfica
      api-patente="{{route('api.patente',['id'=>$patente->numero_patente]) }}"
      api-patente-modifica="{{route('api.patente.modifica',['numero'=>$patente->numero_patente])}}"
      api-patente-persone="{{route('api.patente.persone')}}"
      api-patente-categorie="{{route('api.patente.categorie')}}" >
      <template slot="persona-info">
         <div class="row">
					<div class="col-md-6">
						<label for="numero_patente">Persona:</label>
						<input type="text" class="form-control" value="{{$patente->persona->nominativo}}" disabled>
					</div>

					<div class="col-md-6">
						<label for="nome_cognome">Nome Cognome:</label>
						<input type="text" class="form-control" value="{{$patente->persona->datipersonali->nome}} {{$patente->persona->datipersonali->cognome}}" disabled>
					</div>
        </div> 
        
				<div class="row">
					<div class="col-md-6">
						<label for="data_nascita">Data di nascita:</label>
					  <input type="text" class="form-control" value="{{$patente->persona->datipersonali->data_nascita}}" disabled> 
					</div>
					<div class="col-md-6">
						<label for="luogo_nascita">Luogo di nascita:</label>
						<input type="text" class="form-control" value="{{$patente->persona->datipersonali->provincia_nascita}}" disabled>
					</div>
        </div>
      </template>
</patente-modfica>
  
@endsection
