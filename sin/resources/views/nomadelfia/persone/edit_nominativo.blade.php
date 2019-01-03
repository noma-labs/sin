@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Dati anagrafici'])
<div class="row justify-content-md-center">
    <div class="col col-lg-2">
      1 of 3
    </div>
    <div class="col-md-auto">
      Variable width content
    </div>
    <div class="col col-lg-2">
      3 of 3
    </div>
  </div>
  
<div class="row justify-content-md-center">
    <div class="col-md-8">
    <form class="form" method="POST" action="{{ route('nomadelfia.persone.nominativo.modifica', ['idPersona' =>$persona->id]) }}" >      
    {{ csrf_field() }}
        <div class="form-group row">
          <label for="inputPassword" class="col-md-3 col-form-label">Nominativo Attuale:</label>
          <div class="col-md-4">
          <input type="text" class="form-control" name="nominativo" value="{{old('nominativo') ? old('nominativo'): $persona->nominativo}}">
          </div>
          <!-- <div class="col-md-5"> -->
            <button type="submit" class="btn btn-success col-md-2 " name="operazione" value="modifica">Salva</button>
          <!-- </div> -->
        </div>
        <div class="form-group row">
          <label for="inputPassword" class="col-md-3 col-form-label">Nuovo Nominativo:</label>
          <div class="col-md-4">
          <input type="text" class="form-control" name="nuovonominativo" value="{{old('nuovonominativo')}}">
          </div>
          <!-- <div class="col-md-5"> -->
             <button type="submit" class="btn btn-success col-md-2 " name="operazione" value="nuovo">Cambia</button>
          <!-- </div> -->
        </div>       
      </form>
    </div>
    
    <div class="col-md-4">
    <div class="card">
          <h5 class="card-header">Storico nominativi</h5>
        <div class="card-body">
          @forelse  ($persona->nominativiStorici()->get() as $nominativo)
             <li>{{$nominativo->nominativo}}</li>
          @empty
          </ol>       
          <p class="text-danger">non ci sono nominativi storici</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>


@endsection
