@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('archivio')
<div class="row">
    <div class="col-md-6 offset-md-3">
    <div class="card-deck">
        <div class="card ">
            <div class="card-header">
                Scuola A.S.  {{$anno->scolastico}}
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush ">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <p>Responsabile Scuola</p>
                         @if ($resp)
                                @include("nomadelfia.templates.persona", ['persona' => $resp])
                            @else
                                Non presente
                            @endif
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
                <form class="form" method="POST"  id="formStampa" action="{{ route('scuola.stampa') }}" >
                    {{ csrf_field() }}
                    <div class="form-check">
                        <input class="form-check-input" type="hidden" value="scuola" id="defaultCheck1"  name="elenchi[]" checked>
                    </div>
                </form>
                <button class="btn btn-primary" form="formStampa">Stampa</button>
            </div>
        </div>
        </div>
        </div>

    </div>
@endsection
