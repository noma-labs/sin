<div class="row">
    <div class="col-md-6">
        <p class="fw-bold">
            Uomini maggiorenni {{ count($maggiorenni->uomini) }}
        </p>
        <div class="row">
            @foreach (collect($maggiorenni->uomini)->chunk(60) as $chunk)
                <div class="col-md-6">
                    @foreach ($chunk as $uomo)
                        <div>{{ $uomo->nominativo }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-6">
        <p class="fw-bold">
            Donne maggiorenni {{ count($maggiorenni->donne) }}
        </p>
        <div class="row">
            @foreach (collect($maggiorenni->donne)->chunk(60) as $chunk)
                <div class="col-md-6">
                    @foreach ($chunk as $donna)
                        <div>{{ $donna->nominativo }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
