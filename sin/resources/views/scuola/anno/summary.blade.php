@extends('scuola.index-anno')

@section('title', 'Gestione Scuola')

@section('archivio')
    @include('partials.header', ['title' => "Gestione Anno Scolastico"])

<div class="row">
    <div class="col-md-12">
        <div class="card-deck">
            <div class="card">
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
                </div>
            </div>
        </div>
    </div>
</div>
@include('scuola.templates.aggiungiClasse',["anno"=>$anno])

@foreach ($classi->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $classe)
            <div class="col-md-4">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="heading{{$classe->id}}">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse"
                                        data-target="#collapse{{$classe->id}}" aria-expanded="true"
                                        aria-controls="collapse{{$classe->id}}">
                                    {{ $classe->tipo->nome }}
                                    <span class="badge badge-primary badge-pill">{{ $classe->alunni()->count() }}</span>
                                </button>
                            </h5>
                        </div>
                        <div id="collapse{{$classe->id}}" class="collapse" aria-labelledby="heading{{$classe->id}}"
                             data-parent="#accordion">
                            <div class="card-body">
                                <div>Alunni</div>
                                <ul>
                                    @foreach($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita) @include('nomadelfia.templates.persona', ['persona'=>$alunno])
                                            @liveRome($alunno)
                                            <span class="badge badge-warning">Roma</span>
                                            @endliveRome
                                        </li>
                                    @endforeach
                                </ul>
                                <a class="btn btn-primary" href="{{ route('scuola.classi.show',$classe->id)}}">Dettaglio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
@endsection
