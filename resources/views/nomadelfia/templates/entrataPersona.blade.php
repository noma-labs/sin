<form class="form" method="POST" action="{{ route('nomadelfia.persone.inserimento.entrata.scelta.view', ['idPersona' =>$persona->id])}}">
    {{ csrf_field() }}
    <persona-entrata
            api-nomadelfia-famiglie="{{route('api.nomadeflia.famiglie')}}"
            api-nomadelfia-persona="{{route('api.nomadelfia.persona', ['id'=>$persona->id])}}"
            api-nomadelfia-gruppi="{{route('api.nomadeflia.gruppi')}}"
    >
    </persona-entrata>

    <div class="row my-2">
        <div class="col-auto">
            <button type="submit" class="btn btn-block btn-primary">Salva</button>
        </div>
    </div>
</form>