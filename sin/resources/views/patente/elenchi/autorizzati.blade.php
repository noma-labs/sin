
<!-- @foreach ($patentiAutorizzati->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $patente)
            <div class="col-md-4">{{$patente->persona->nominativo}}</div>
        @endforeach
    </div>
@endforeach -->


<div class="row">
  @foreach ($patentiAutorizzati->chunk(85) as $chunk)
  <div class="col-md-4 mr-4">
        <div class="row mb-3">
          <div class="col-md-6 small">
            Nome Cognome
          </div>
          <div class="col-md-3">
            Patente
          </div>
          <div class="col-md-4 text-right small">
            Data nascita
          </div>
        </div>
      @foreach ($chunk as $patente)
      <div class="row">
          <div class="col-md-6 small ">
          @isset($patente->persona->datipersonali->nome) 
           {{ $patente->persona->datipersonali->nome}}
          @endisset
          @isset($patente->persona->datipersonali->cognome)
            {{ $patente->persona->datipersonali->cognome}}
           @endisset
          </div>
          <div class="col-md-3">
             {{$patente->categorieAsString()}}
          </div>
          <div class="col-md-4 text-right small">
            @isset($patente->persona->datiPersonali->data_nascita)
              {{$patente->persona->datiPersonali->data_nascita}}
            @endisset
          </div>
      </div>
      @endforeach
    </div>
  @endforeach
  </div>
  
