<div class="row">
  <div class="col-md-6">
    <p class="font-weight-bold">Uomini maggiorenni {{$maggiorenniUomini->count()}}</p>
    <div class="row">
      @foreach ($maggiorenniUomini->get()->chunk(60) as $chunk)
        <div class="col-md-6">
            @foreach ($chunk as $uomo)
                <div>{{ $uomo->nominativo }}</div>
            @endforeach
        </div>
      @endforeach
    </div>
  </div>
  <div class="col-md-6">
    <p class="font-weight-bold">Donne maggiorenni {{$maggiorenniDonne->count()}}</p>
    <div class="row">
      @foreach ($maggiorenniDonne->get()->chunk(60) as $chunk)
          <div class="col-md-6">
            @foreach ($chunk as $donna)
              <div>{{ $donna->nominativo }}</div>
              @endforeach
          </div>
        @endforeach
      </div>
    </div>
</div>