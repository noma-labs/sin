@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('archivio')

    <div class="card-deck">
        <div class="card ">
            <div class="card-header">
                Gestione Scuola Familiare
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush ">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{route('nomadelfia.popolazione')}}"> Responabile</a>
                        <span class="badge badge-primary badge-pill">AAA </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{route('scuola.classi')}}"> Classi</a>
                        <span class="badge badge-primary badge-pill">12 </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{route('nomadelfia.popolazione')}}"> Totale studenti</a>
                        <span class="badge badge-primary badge-pill">XXX </span>
                    </li>

                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.persone') }}" class=" text-center  btn btn-primary">Entra</a>
            </div>
        </div>

    </div>
@endsection
