<div class="text-center">
    <img src="{{ asset("/images/logo-noma.png") }}" alt="Nomadelfia" />

    <h1>Popolazione di Nomadelfia</h1>
    <h4>{{ Carbon::now(new DateTimeZone("Europe/Rome"))->toDateString() }}</h4>

    <h5>
        Totale:{{ App\Nomadelfia\Models\PopolazioneNomadelfia::totalePopolazione() }}
    </h5>
</div>
