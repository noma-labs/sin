@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Nominativo'])
<div class="row justify-content-md-center">
    <div class="col-md-4">
      <div class="card">
          <h5 class="card-header">Modifica nominativo attuale</h5>
          <div class="card-body">
          <form class="form" method="PUT" action="{{ route('nomadelfia.persone.nominativo.modifica', ['idPersona' =>$persona->id]) }}" >
            {{ csrf_field() }}
              <div class="form-group">
                <label for="exampleInputEmail1">Nominativo attuale</label>
                <input type="text" class="form-control" name="nominativo" value="{{old('nominativo') ? old('nominativo'): $persona->nominativo}}">
                <small id="emailHelp" class="form-text text-muted">Nominativo attualmente in uso.</small>
                <button type="submit" class="btn btn-success col-md-2 " name="operazione" value="modifica">Salva</button>
              </div>
              </form>
          </div> <!-- end card body-->
        </div> <!-- end card modifica nominativo-->
    </div> <!-- end col -->

    <div class="col-md-4">
      <div class="card my-2s">
          <h5 class="card-header">Assenga nuovo nominativo</h5>
          <div class="card-body">
          <form class="form" method="POST" action="{{ route('nomadelfia.persone.nominativo.assegna', ['idPersona' =>$persona->id]) }}" >      
            {{ csrf_field() }}
            <div class="form-group">
                <label for="exampleInputEmail1">Nuovo nominativo</label>
                <input type="text" class="form-control" name="nuovonominativo" value="{{old('nuovonominativo')}}">
                <small id="emailHelp" class="form-text text-muted">Il nuovo Nominativo verrà assegnato alla persona. Il nominativo attuale verrà messo nella lista dei nominativi storici..</small>
                <button type="submit" class="btn btn-success col-md-2 " name="operazione" value="nuovo">Assegna</button>
              </div>
            </form>
          </div> <!-- end card body-->
        </div> <!-- end card crea nuovo nominativo  -->
    </div> <!-- end col -->
</div> <!-- end row nominativo attuale e nuovo nominativi -->

<div class="row  justify-content-md-center my-2">
  <div class="col-md-8">
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
      </div> <!-- end card storico nominativo -->
  </div> <!-- end col -->
</div> <!-- end row storico nominativi -->



@endsection
