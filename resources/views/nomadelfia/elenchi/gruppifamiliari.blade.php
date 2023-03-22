<h2>Gruppi Familiari</h2>
@foreach ($gruppifamiliari->get()->chunk(4) as $chunk)
    <div class="row">
        @foreach ($chunk as $gruppo)
            <div class="col-md-3"> 
            <p class="font-weight-bold">{{$gruppo->nome}} {{$gruppo->persone()->count()}}</p>
            @if ($gruppo->capogruppoAttuale())
            <p> Capogruppo: <span class="font-weight-bold">{{$gruppo->capogruppoAttuale()->nominativo}}</span></p> 
            @else
            <p class="text-danger">Senza capogruppo</p> 
            @endif
            @foreach($gruppo->Single() as $famiglia)
            <p class="font-weight-bold"> {{$famiglia->nominativo}}</p> 
            @endforeach
            @foreach($gruppo->Famiglie() as $famiglia_id => $componenti)
                <ul class="list-unstyled">
                    @foreach($componenti as $componente)
                    @if(str_contains($componente->posizione_famiglia,"FIGLIO"))
                        <li> @year($componente->data_nascita) {{$componente->nominativo}}</li>
                    @else
                        <li class="font-weight-bold">{{$componente->nominativo}}</li>
                    @endif
                    @endforeach
                </ul>
            @endforeach
            </div>
        @endforeach
    </div>
@endforeach