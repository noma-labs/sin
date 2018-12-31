@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Modifica Dati anagrafici'])

<div class="row justify-content-center">
    <div class="col-4">
      <form class="form" method="POST" action="{{ route('nomadelfia.persone.nominativo.modifica', ['idCliente' =>$persona->id]) }}" >
      {{ csrf_field() }}
        <div class="form-group">
          <label >Nominativo</label>
          <input type="text" class="form-control" name="nominativo" value="{{old('nominativo') ? old('nominativo'): $persona->nominativo}}">
        </div>
        <div class="form-group">
              <select class="form-control"  name="categoria" type="text">
              <option value='{{ $persona->categoria_id }}' selected>{{ $persona->categoria->nome }}</option>
               @foreach ($categorie as $cat)
                    @if($persona->categoria_id != $cat->id)
                      @if(old('categoria') == $cat->id)
                     <option value="{{$cat->id}}" selected> {{ $cat->nome}}</option>
                     @else
                     <option value="{{$cat->id}}" > {{ $cat->nome}}</option>
                     @endif

                    @endif
              @endforeach
             </select>
        </div>

          <button class="btn btn-danger">Torna indietro</button>
          <button class="btn btn-success" type="submit">Salva Modifiche</button>
        </div>
      </form>
    </div>
  </div>

@endsection
