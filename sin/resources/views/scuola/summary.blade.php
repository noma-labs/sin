@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('archivio')
<div class="row justify-content-md-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                   A.S. {{$lastAnno->scolastico}}
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
                            Studenti
                            <span class="badge badge-primary badge-pill">{{$alunni}} </span>
                        </li>
                        @foreach ($cicloAlunni as $c)
                            <li class="list-group-item d-flex justify-content-end  align-items-center ">
                                <p class="m-2">   {{ucfirst($c->ciclo)}}</p>
                                <span class="badge badge-primary badge-pill">{{$c->count}} </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer">
                    <div class="row justify-content-between">
                        <div class="col-4">
                            <a class="btn btn-primary" href="{{ route('scuola.anno.show',$lastAnno->id)}}">Dettaglio</a>
                            <my-modal modal-title="Esporta Elenchi" button-title="Esporta Elenchi" button-style="btn-secondary my-2">
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
                        </div>
                        <div class="col-4">
                            <my-modal modal-title="Aggiungi A.Scolastico" button-title="Crea nuovo Anno Scolastico" button-style="btn-secondary my-2">
                                <template slot="modal-body-slot">
                                    <form class="form" method="POST" id="formComponente" action="{{ route('scuola.anno.aggiungi') }}" >
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label for="annoInizio">Inzio Anno</label>
                                            <input type="text" name="anno_inizio" class="form-control" id="annoInizio" aria-describedby="Anno inizio" placeholder="e.g., 2022">
                                        </div>
                                    </form>
                                </template>
                                <template slot="modal-button">
                                    <button class="btn btn-primary" form="formComponente">Salva</button>
                                </template>
                            </my-modal>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>
@endsection
