@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => "Gestione Famiglie di ".$persona->nominativo])

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                Famiglia Attuale
            </div>
            <div class="card-body">
                @if($attuale)
                <div class="row">
                    <p class="col-md-3 font-weight-bold"> Nome Famigla </p>
                    <p class="col-md-3 font-weight-bold"> Posizione Famiglia</p>
                    <p class="col-md-3 font-weight-bold"> Operazioni</p>
                </div>
                <div class="row">
                    <p class="col-md-3"><a
                                href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$attuale->id])}}">{{$attuale->nome_famiglia}} </a>
                    </p>
                    <p class="col-md-3">{{$attuale->pivot->posizione_famiglia }} </p>
                    @else
                    <p class="text-danger">Nessuna famiglia</p>
                    @endif

                    <my-modal modal-title="Sposta in una nuova Famiglia" button-title="Sposta" button-style="btn-success">
                        <template slot="modal-body-slot">
                            <form class="form" method="POST" id="formFamigliaSposta"
                                  action="{{ route('nomadelfia.personae.famiglie.sposta', ['idPersona' =>$persona->id]) }}">
                                {{ csrf_field() }}
                                @if($attuale)
                                <h5 class="my-2">Completa dati della famiglia attuale: {{$attuale->nome}}</h5>
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-4 col-form-label">Data uscita
                                        famiglia</label>
                                    <div class="col-sm-8">
                                        <date-picker :bootstrap-styling="true" value="" format="yyyy-MM-dd"
                                                     name="old_data_uscita"></date-picker>
                                        <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con
                                            la data di entrata nella nuova famiglia .</small>
                                    </div>
                                </div>
                                <hr>
                                @endif

                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-4 col-form-label">Famiglia</label>
                                    <div class="col-sm-8">
                                        <select name="new_famiglia_id" class="form-control">
                                            <option value="" selected>---Seleziona famiglia---</option>
                                            @foreach(Domain\Nomadelfia\Famiglia\models\Famiglia::ordered() as $famiglia)
                                            <option value="{{$famiglia->id}}">{{$famiglia->nome_famiglia}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputPassword" class="col-sm-4 col-form-label">Data entrata
                                        famiglia</label>
                                    <div class="col-sm-8">
                                        <date-picker :bootstrap-styling="true"
                                                     value="{{ Carbon::now()->toDateString()}}" format="yyyy-MM-dd"
                                                     name="new_data_entrata"></date-picker>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <label for="staticEmail" class="col-sm-4 col-form-label">Posizione Famiglia</label>
                                    <div class="col-sm-8">
                                        <select name="new_posizione_famiglia" class="form-control">
                                            <option value="" selected>---Seleziona posizione---</option>
                                            @foreach(Domain\Nomadelfia\Famiglia\models\Famiglia::getEnum('Posizione') as $posizione)
                                            <option value="{{ $posizione }}">{{ $posizione }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </template>
                        <template slot="modal-button">
                            <button class="btn btn-success" form="formFamigliaSposta">Salva</button>
                        </template>
                    </my-modal>

                    @if($persona->isMaggiorenne() && !$attuale)
                        @include('nomadelfia.templates.nuovoMatrimonio')
                    @endif
                </div>  <!--end card body-->
            </div>
            </div>

            <div class="card my-3">
                <div class="card-header">
                    Storico famiglie
                </div>
                <div class="card-body">
                    <div class="row">
                        <p class="col-md-3 font-weight-bold"> Nome famiglia</p>
                        <p class="col-md-3 font-weight-bold"> Posizione</p>
                    </div>

                    @forelse($storico as $famigliaStorico)

                    <div class="row">
                        <p class="col-md-3"> {{$famigliaStorico->nome_famiglia}}</p>
                         <p class="col-md-3">{{$famigliaStorico->pivot->posizione_famiglia }} </p>
                    </div>

                    @empty
                    <p class="text-danger">Nessuna famiglia nello storico</p>
                    @endforelse
                </div>  <!--end card body-->
            </div> <!--end card -->

    </div> <!--end col -md-6 -->
</div> <!--end row justify-->

@endsection
