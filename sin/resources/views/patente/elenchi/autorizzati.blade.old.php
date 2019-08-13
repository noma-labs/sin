
<!-- @foreach ($patentiAutorizzati->chunk(2) as $chunk)
    <div class="row">
        @foreach ($chunk as $patente)
            <div class="col-md-4">
              @isset($patente->persona->nome)
              {{ $patente->persona->nome}}
              @endisset

              @isset($patente->persona->cognome)
                {{ $patente->persona->cognome}}
              @endisset

              {{$patente->categorieAsString()}}

              @isset($patente->persona->data_nascita)
                {{$patente->persona->data_nascita}}
              @endisset
          </div>
        @endforeach
    </div>
@endforeach
 -->


<div class="row">
  @foreach ($patentiAutorizzati->chunk(40) as $chunk)
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
          <div class="col-md-6">
          @isset($patente->persona->nome)
           {{ $patente->persona->nome}}
          @endisset
          @isset($patente->persona->cognome)
            {{ $patente->persona->cognome}}
           @endisset
          </div>
          <div class="col-md-2">
             {{$patente->categorieAsString()}}
          </div>
          <div class="col-md-4 text-right">
            @isset($patente->persona->data_nascita)
              {{$patente->persona->data_nascita}}
            @endisset
          </div>
      </div>
      @endforeach
    </div>
  @endforeach
  </div>
<!-- 
<div class="row">
  <div class="col-md-4">
      NOME E COGNOME
  </div>
  <div class="col-md-4">
    PATENTI
  </div>
  <div class="col-md-4">
    DATA NASCITA
  </div>
</div>

<!-- <div class="page">
            @include('patente.elenchi.autorizzati')
</div> -->
<!-- @foreach ($patentiAutorizzati as $patente)
<div class="row">
    @if($loop->iteration % 40 == 0)
      <div class="page">
        <div class="row">
          <div class="col-md-4">
              NOME E COGNOME
          </div>
          <div class="col-md-4">
            PATENTI
          </div>
          <div class="col-md-4">
            DATA NASCITA
          </div>
        </div>
      </div> 
    @endif
    <div class="col-md-4">
    @isset($patente->persona->nome)
      {{ $patente->persona->nome}}
    @endisset
    @isset($patente->persona->cognome)
      {{ $patente->persona->cognome}}
      @endisset
    </div>
    <div class="col-md-4">
        {{$patente->categorieAsString()}}
    </div>
    <div class="col-md-4">
      @isset($patente->persona->data_nascita)
        {{$patente->persona->data_nascita}}
      @endisset
    </div>
</div>
@endforeach -->
