@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' =>  $persona->nome." ". $persona->cognome])
<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <h5 class="card-header">Assegna Numero elenco </h5>
            <div class="card-body">
                <form class="form" method="POST"
                      action="{{ route('nomadelfia.persone.numelenco.confirm', ['idPersona' =>$persona->id]) }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-9">
                            <label for="exampleInputEmail1">Numero elenco esistenti per la lettera <strong>{{$first}}</strong></label>
                            @if(count($assegnati) >0 )
                            <select class="form-control">
                                @foreach ($assegnati as $p)
                                    <option value="{{$p}}"> {{$p->numero_elenco}} {{$p->nome}} {{$p->cognome}}</option>
                                @endforeach
                            </select>
                            @else
                               <p class="text-danger">Non ci sono numero elenco esistenti</p>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label for="numeroElenco">Nuovo</label>
                            <input class="form-control" value="{{$propose}}" name="numero_elenco">
                        </div>
                    </div>
                    <button class="btn btn-danger">Salva</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
