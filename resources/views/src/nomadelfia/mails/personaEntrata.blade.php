<x-mail::message>
    ## Aggiornamento Anagrafe {{ Carbon::now()->toDateString() }} La seguente
    persona è entrata in Nomadelfia:
    <x-mail::panel>
        <p>
            Cognome:
            <strong>{{ $persona->cognome }}</strong>
        </p>
        <p>
            Nome:
            <strong>{{ $persona->nome }}</strong>
        </p>
        <p>
            Luogo Nascita:
            <strong>{{ $persona->provincia_nascita }}</strong>
        </p>
        <p>
            Data Nascita:
            <strong>{{ $persona->data_nascita }}</strong>
        </p>

        <p>
            Data Entrata:
            <strong>{{ $data_entrata }}</strong>
        </p>
        <p>
            Gruppo:
            <strong>{{ $gruppo->nome }}</strong>
        </p>
        <p>
            Famiglia:
            <strong>{{ $famiglia ? $famiglia->nome_famiglia : "" }}</strong>
        </p>
        <p>
            N°Elenco:
            <strong>{{ $persona->numero_elenco }}</strong>
        </p>
    </x-mail::panel>

    <x-mail::button
        :url="route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->id])"
        color="success"
    >
        Vedi persona
    </x-mail::button>

    Saluti,
    <br />
    {{ config("app.name") }}
</x-mail::message>
