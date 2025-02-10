<h2>Aziende</h2>
@foreach ($aziende->get()->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $azienda)
            <div class="col-md-4">
                <p class="fw-bold">
                    {{ $azienda->nome_azienda }}
                    {{ $azienda->lavoratoriAttuali()->count() }}
                </p>

                @foreach ($azienda->lavoratoriAttuali as $persona)
                    <div>{{ $persona->nominativo }}</div>
                @endforeach
            </div>
        @endforeach
    </div>
@endforeach
