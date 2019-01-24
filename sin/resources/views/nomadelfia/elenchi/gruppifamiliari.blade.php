<h2>Gruppi Familiari</h2>
@foreach ($gruppifamiliari->get()->chunk(4) as $chunk)
    <div class="row">
        @foreach ($chunk as $gruppo)
            <div class="col-md-3"> 
            <p class="font-weight-bold">{{$gruppo->nome}} {{$gruppo->persone()->count()}}</p>
            @if ($gruppo->capogruppoAttuale())
            <p class="font-weight-bold"> Capogruppo: {{$gruppo->capogruppoAttuale()->nominativo}}</p> 
            @else
            <p class="text-danger">Senza capogruppo</p> 
        @endif
        @foreach($gruppo->famiglie as $famiglia)
            @if ($famiglia->single())
            <div class="font-weight-bold"> {{$famiglia->single()->nominativo}}</div>
            @else
            <div class="font-weight-bold">@if ($famiglia->capofamiglia())  {{$famiglia->capofamiglia()->nominativo}} @endif</div>
            <div class="font-weight-bold">@if ($famiglia->moglie())  {{$famiglia->moglie()->nominativo}} @endif</div>
            <ul>
                @foreach($famiglia->figliAttuali as $fig)
                @if($fig)
                <li> @year($fig->data_nascita) {{$fig->nominativo}}</li>
                @endif
                @endforeach
            </ul>
            @endif
            @endforeach
            </div>
        @endforeach
    </div>
@endforeach