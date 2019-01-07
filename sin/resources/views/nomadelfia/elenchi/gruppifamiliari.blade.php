<h2>Gruppi Familiari</h2>
@foreach ($gruppifamiliari->get()->chunk(4) as $chunk)
    <div class="row">
        @foreach ($chunk as $gruppo)
            <div class="col-md-3"> 
            <p class="font-weight-bold">{{$gruppo->nome}} {{$gruppo->persone()->count()}}</p>
            @if ($gruppo->capogruppiAttuali->isNotEmpty())
            <p class="font-weight-bold"> Capogruppo: {{$gruppo->capogruppiAttuali->first()->nominativo}}</p> 
            @else
            <p class="text-danger">Senza capogruppo</p> 
        @endif
        @foreach($gruppo->famiglie as $famiglia)
            @if ($famiglia->single->isNotEmpty())
            <div class="font-weight-bold"> {{$famiglia->single->first()->nominativo}}</div>
            @else
            <div class="font-weight-bold">@if ($famiglia->capofamiglia->isNotEmpty())  {{$famiglia->capofamiglia()->first()->nominativo}} @endif</div>
            <div class="font-weight-bold">@if ($famiglia->moglie->isNotEmpty())  {{$famiglia->moglie->first()->nominativo}} @endif</div>
            <ul>
                @foreach($famiglia->figli as $figlio)
                <li>{{Carbon::parse($figlio->data_nascita)->year}} {{$figlio->nominativo}}</li>
                @endforeach
            </ul>
            @endif
            @endforeach
            </div>
        @endforeach
    </div>
@endforeach