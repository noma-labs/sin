
<h2> Figli minorenni {{$minorenni->total}}</h2> 
@foreach ($minorenni->anno->chunk(4) as $anni)
  <div class="row">
        @foreach ($anni as $key =>$anno)
          <div class="col-md-3">
            <span class="font-weight-bold">Nati {{$key}}</span>
              @foreach ($anno as $sesso)
                @foreach($sesso as $persona)
                  <div>{{ $persona->nominativo }}</div>
                @endforeach
                @if (!$loop->last)
                  <hr size="4">
                @endif
              @endforeach
          </div>
        @endforeach
    </div>
@endforeach
