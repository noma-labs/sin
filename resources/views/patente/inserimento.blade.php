@extends("patente.index")

@section("content")
    <sin-header title="Inserisci nuova Patente"></sin-header>

    <patente-inserimento
        api-patente-persone="{{ route("api.patente.persone.senzapatente") }}"
        api-patente-categorie="{{ route("api.patente.categorie") }}"
        api-patente-cqc="{{ route("api.patente.cqc") }}"
        api-patente-create="{{ route("api.patente.create") }}"
        api-patente-rilascio="{{ route("api.patente.rilascio") }}"
    ></patente-inserimento>
@endsection
