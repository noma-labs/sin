@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Assegna Numero elenco'])
<div class="row justify-content-md-center">
    <div class="col-md-4">
        <div class="card">
            <h5 class="card-header">Assegna Numero elenco </h5>
            <div class="card-body">
                <form class="form" method="POST"
                      action="{{ route('nomadelfia.persone.nominativo.modifica', ['idPersona' =>$persona->id]) }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-9">
                            <label for="exampleInputEmail1">precedenti</label>
                            @if(count($assegnati) >0 )
                            <select class="form-control">
                                @foreach ($assegnati as $p)
                                    <option value="{{$p}}"> {{$p->data_nascita}} {{$p->nome}} {{$p->cognome}}</option>
                                @endforeach
                            </select>
                            @else
                               <p class="text-danger">Non ci sono numero elenco presenti</p>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label for="numeroElenco">Nuovo</label>
                            <input class="form-control" value="{{$propose}}" name="numero_elenco" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
