
@foreach ($patentiAutorizzati->chunk(50) as $chunk)
<div class="page">
<table class="table table-hover table-bordered table-sm table-striped"  style="background-color:white;">
    <thead class="thead-inverse">
      <tr>
        <th width="5%">#</th>
        <th>Nome Cognome</th>
        <th>Patente</th> 
        <th>Data Nascita</th>
      </tr>
  </thead>
  @foreach ($chunk as $patente)
   <tr>
   <td>@if ($loop->parent)
            {{$loop->parent->index * 50 + $loop->iteration }}
        @endif</td>
     <td>@isset($patente->persona->nome)
           {{ $patente->persona->nome}}
          @endisset
          @isset($patente->persona->cognome)
            {{ $patente->persona->cognome}}
           @endisset</td>
    <td>  {{$patente->categorieAsString()}}</td> 
    <td> @isset($patente->persona->data_nascita)
              {{$patente->persona->data_nascita}}
            @endisset</td>
  </tr>
    @endforeach
</table>
</div>
@endforeach  