<h1 class="font-weight-bold">NOMADELFIA</h1>

<p>Via Nomadelfia 1, Roselle 58100 Grosseto (GR)</p>
<p>E-mail: presidente@nomadelfia.it</p>
<p>Internet: www.nomadelfia.it</p>

<h3 class="text-right py-5">
    Nomadelfia, {{ Carbon::now()->format("d/m/Y") }}
    <h3>
        <h3 class="text-left py-5">
            Il sottoscritto

            @if ($presidente->cognome)
                {{ $presidente->cognome }} {{ $presidente->nome }} nato a
                {{ $presidente->provincia_nascita }} il
                {{ $presidente->data_nascita }},
            @else
                    ____________ nato a _______ il _________,
            @endif
            residente in Via Nomadelfia 1, Roselle 58100 Grosseto (GR), in
            qualità di Presidente pro-tempore della Associazione Nomadelfia, con
            sede in Grosseto
        </h3>

        <h1 class="text-center font-weight-bold py-5">DICHIARA</h1>

        <h3 class="text-left py-5">
            che le persone sottoelencate appartengono all'associazione e
            pertanto sono autorizzate a condurre gli autoveicoli di proprietà
            della stessa.
        </h3>

        <h2 class="text-right pt-5">Il presidente</h2>
        <div class="text-right">
            {{-- <img src="{{url('/images/patente/firma-presidente.jpg')}}" width="200" alt=""/> --}}
            <img
                src="{{ url("/images/patente/timbro.jpg") }}"
                width="200"
                alt=""
            />
        </div>
        <h2 class="text-right">
            {{ $presidente->nome }} {{ $presidente->cognome }} di Nomadelfia
        </h2>
    </h3>
</h3>
