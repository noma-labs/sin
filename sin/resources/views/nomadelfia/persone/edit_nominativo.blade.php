@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Dati anagrafici'])
<div class="row">
    <div class="col-md-8">
    <form class="form" method="POST" action="{{ route('nomadelfia.persone.nominativo.modifica', ['idPersona' =>$persona->id]) }}" >      
    {{ csrf_field() }}
        <div class="form-group row">
          <label for="inputPassword" class="col-md-2 col-form-label">Nominativo:</label>
          <div class="col-md-4">
          <input type="text" class="form-control" name="nominativo" value="{{old('nominativo') ? old('nominativo'): $persona->nominativo}}">
          </div>
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary " name="operazione" value="modifica">Modifica nominativo attuale</button>
          </div>
        </div>
        <h3> Assegna un nuovo Nominativo</h3>
        <div class="form-group row">
          <label for="inputPassword" class="col-md-4 col-form-label">Nuovo Nominativo:</label>
          <div class="col-md-4">
          <input type="text" class="form-control" name="nuovonominativo" value="{{old('nuovonominativo')}}">
          </div>
        </div>       
         <button type="submit" class="btn btn-primary col-md-2" name="operazione" value="nuovo">Assegna nuovo</button>
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
