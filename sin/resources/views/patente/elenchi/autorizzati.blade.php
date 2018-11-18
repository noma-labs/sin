@extends('patente.index')

<style type="text/css" media="print">
    div.page
    {
        page-break-after: always;
        page-break-inside: avoid;
    }
</style>

@section('archivio')
<div class="container">
<div class="row">
  @foreach ($patentiAutorizzati->chunk(65) as $chunk)
  <div class="col-md-6">
      @foreach ($chunk as $patente)
      <div class="row">
          <div class="col-md-4">
             {{$patente->persona->nominativo}}
          </div>
          <div class="col-md-4">
             {{$patente->categorieAsString()}}
          </div>
          <div class="col-md-4 text-right">
            @isset($patente->persona->datiPersonali->data_nascita)
              {{$patente->persona->datiPersonali->data_nascita}}
            @endisset
          </div>
      </div>
      @endforeach
      </div>
  @endforeach
  </div>
</div>
@endsection
