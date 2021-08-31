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
                                Non Assegnato
                            @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <p>Studenti</p>
                        <ul class="list-group">
                            @foreach ($cicloAlunni as $c)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{route('scuola.classi')}}"> {{ucfirst($c->ciclo)}}</a>
                                    <span class="badge badge-primary badge-pill">{{$c->count}} </span>
                                </li>
                            @endforeach
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Totale
                                    <span class="badge badge-primary badge-pill">{{count($alunni)}} </span>
                                </li>
                        </ul>
                    </li>




                </ul>
            </div>
            <div class="card-footer">
                <my-modal modal-title="Esporta Elenchi" button-title="Esporta Elenchi" button-style="btn-primary my-2">
                    <template slot="modal-body-slot">
                        <form class="form" method="POST"  id="formStampa"  action="{{ route('scuola.stampa') }}" >
                            {{ csrf_field() }}
                            <p>Seleziona gli elenchi da stampare:</p>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="studenti" id="defaultCheck1"   name="elenchi[]" checked>
                                <label class="form-check-label" for="defaultCheck1">
                                    Elenco Studenti
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="coordinatori" id="defaultCheck1"   name="elenchi[]" checked>
                                <label class="form-check-label" for="defaultCheck1">
                                    Elenco Coordinatori
                                </label>
                            </div>
                        </form>
                    </template>
                    <template slot="modal-button">
                        <button class="btn btn-success" form="formStampa">Esporta (.doc) </button>
                    </template>
                </my-modal>

{{--                <form class="form" method="POST"  id="formStampa" action="{{ route('scuola.stampa') }}" >--}}
{{--                    {{ csrf_field() }}--}}

{{--                </form>--}}
{{--                <button class="btn btn-primary" form="formStampa">Esporta (.docx)</button>--}}
            </div>
        </div>
        </div>
        </div>

    </div>
@endsection
