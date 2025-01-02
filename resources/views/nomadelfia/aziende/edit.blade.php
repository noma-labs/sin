@extends("nomadelfia.index")

@section("content")
    @include("partials.header", ["title" => "Modifica azienda: " . $azienda->nome_azienda])
    <azienda-edit
        url_aggiungi="{{ route("api.nomadelfia.azienda.aggiungi.lavoratore") }}"
        base_url="/api/nomadelfia/aziende"
        url_azienda_edit="{{ route("api.nomadeflia.azienda.edit", $azienda->id) }}"
        url_persona
        url_mansioni="{{ route("api.nomadeflia.azienda.mansioni") }}"
        url_stati="{{ route("api.nomadeflia.azienda.stati") }}"
        url_modifica_lavoratore="{{ route("api.nomadeflia.azienda.modifica.lavoratore") }}"
        id_azienda="{{ $azienda->id }}"
    ></azienda-edit>
@endsection
