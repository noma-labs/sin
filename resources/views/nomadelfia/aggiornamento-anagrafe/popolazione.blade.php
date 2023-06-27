@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Aggiornamento Anagrafe'])

<div class="row justify-content-center my-2">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Usciti <span class="badge badge-primary"> {{count($usciti)}}</span></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <p class="col-md-2  font-weight-bold"> Nominativo</p>
                    <p class="col-md-2  font-weight-bold"> Nascita</p>
                    <p class="col-md-2  font-weight-bold"> Data Entrata</p>
                    <p class="col-md-2  font-weight-bold"> Data Uscita</p>
                    <p class="col-md-2  font-weight-bold"> N cartella </p>
                </div>

                @forelse($usciti as $uscito)

                <div class="row">
                    <p class="col-md-2"> @include('nomadelfia.templates.persona', ['persona'=>$uscito->subject]) </p>
                    <p class="col-md-2">{{$uscito->getExtraProperty('data_entrata')}}</p>
                    <p class="col-md-2">{{$uscito->getExtraProperty('data_nascita')}}</p>
                    <p class="col-md-2">{{$uscito->getExtraProperty('data_uscita')}}</p>
                    <p class="col-md-2">{{$uscito->getExtraProperty('numero_elenco')}}</p>
                    <p class="col-md-2"></p>
                </div>

                @empty
                <p class="text-danger">Nessuna nuova uscita</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Entrati <span class="badge badge-primary"> {{count($entrati)}}</span></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <p class="col-md-2 font-weight-bold"> Nominativo</p>
                    <p class="col-md-2 font-weight-bold"> Luogo nascita</p>
                    <p class="col-md-1 font-weight-bold"> Nascita</p>
                    <p class="col-md-1 font-weight-bold"> Entrata </p>
                    <p class="col-md-2 font-weight-bold"> Gruppo </p>
                    <p class="col-md-2 font-weight-bold"> Famiglia </p>
                    <p class="col-md-2 font-weight-bold"> N Cartella</p>
                </div>
                @forelse($entrati as $entrato)
                <div class="row">
                    <p class="col-md-2"> @include('nomadelfia.templates.persona', ['persona'=>$entrato->subject])</p>
                    <p class="col-md-2">{{$entrato->getExtraProperty('luogo_nascita')}}</p>
                    <p class="col-md-1">{{$entrato->getExtraProperty('data_nascita')}}</p>
                    <p class="col-md-1">{{$entrato->getExtraProperty('data_entrata')}}</p>
                    <p class="col-md-2">{{$entrato->getExtraProperty('gruppo')}}</p>
                    <p class="col-md-2">{{$entrato->getExtraProperty('nome_famiglia')}}</p>
                    <p class="col-md-2">{{$entrato->getExtraProperty('numero_elenco')}}</p>
                </div>
                @empty
                <p class="text-danger">Nessuna nuova entrata</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
