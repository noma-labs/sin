@extends('nomadelfia.elenchi.layout')

@section('maggiorenni')
<div class="container">
    <div class="row">
      <div class="col-md-6">
        <p class="font-weight-bold">Uomini maggiorenni {{$maggiorenniUomini->count()}}</p>
        <div class="row">
          @foreach ($maggiorenniUomini->get()->chunk(60) as $chunk)
            <div class="col-md-6">
                @foreach ($chunk as $uomo)
                    <div class="col-xs-4">{{ $uomo->nominativo }}</div>
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
                     <div class="col-xs-4">{{ $donna->nominativo }}</div>
                     @endforeach
                  </div>
            @endforeach
          </div>
        </div>
    </div>
</div>
@endsection