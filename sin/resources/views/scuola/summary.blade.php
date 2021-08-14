@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('archivio')

    <div class="card-deck">
        <div class="card ">
            <div class="card-header">
                Scuola A.S.  {{$anno->scolastico}}
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush ">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <p>Responsabile Scuola</p>
                        <span class="badge badge-primary badge-pill">xxxx </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="{{route('scuola.classi')}}"> Classi</a>
                        <span class="badge badge-primary badge-pill">{{$anno->classi->count()}} </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <p> Totale studenti</p>
                        <span class="badge badge-primary badge-pill">{{count($alunni)}} </span>
                    </li>

                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.persone') }}" class=" text-center  btn btn-primary">Entra</a>

            </div>
        </div>

    </div>
@endsection
