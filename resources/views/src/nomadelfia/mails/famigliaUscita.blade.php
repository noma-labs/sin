<x-mail::message>
    ## Aggiornamento Anagrafe {{ Carbon::now()->toDateString() }} La famiglia
    {{ $famiglia->nome_famiglia }} è uscita da Nomadelfia

    @foreach ($componenti as $persona)
        <x-mail::panel>
            <p>
                Nome:
                <strong>{{ $persona->nome }}</strong>
            </p>
            <p>
                Cognome:
                <strong>{{ $persona->cognome }}</strong>
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
                <strong>
                    {{ $persona->getDataEntrataNomadelfia() ?: "" }}
                </strong>
            </p>
            <p>
                Data Uscita:
                <strong>{{ $data_uscita->toDateString() }}</strong>
            </p>
            <p>
                Numero di Elenco:
                <strong>{{ $persona->numero_elenco }}</strong>
            </p>
        </x-mail::panel>
    @endforeach

    <x-mail::button
        :url="route('nomadelfia.famiglia.dettaglio',['id'=>$famiglia->id])"
        color="success"
    >
        Vedi Famiglia
    </x-mail::button>

    Saluti,
    <br />
    {{ config("app.name") }}
</x-mail::message>
