<div class="row">
  <div class="col-md-6">
    <p class="font-weight-bold">Uomini maggiorenni {{count($maggiorenniUomini)}}</p>
    <div class="row">
      @foreach (collect($maggiorenniUomini)->chunk(60) as $chunk)
        <div class="col-md-6">
            @foreach ($chunk as $uomo)
                <div>{{ $uomo->nominativo }}</div>
            @endforeach
        </div>
      @endforeach
    </div>
  </div>
  <div class="col-md-6">
    <p class="font-weight-bold">Donne maggiorenni {{count($maggiorenniDonne)}}</p>
    <div class="row">
      @foreach (collect($maggiorenniDonne)->chunk(60) as $chunk)
          <div class="col-md-6">
            @foreach ($chunk as $donna)
              <div>{{ $donna->nominativo }}</div>
              @endforeach
          </div>
        @endforeach
      </div>
    </div>
</div>