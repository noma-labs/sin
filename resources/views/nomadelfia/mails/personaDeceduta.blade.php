<x-mail::message>
    ## Aggiornamento Anagrafe {{ Carbon::now()->toDateString() }} La seguente
    persona è deceduta:
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
            Data Decesso:
            <strong>{{ $data_decesso->toDateString() }}</strong>
        </p>
        <p>
            Data Uscita:
            <strong>{{ $data_decesso->toDateString() }}</strong>
        </p>
        <p>
            N° Elenco:
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
