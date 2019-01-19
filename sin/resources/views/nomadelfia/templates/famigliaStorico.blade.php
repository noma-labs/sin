
@if($famiglia->single())
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
        <div class="row">
            <label class="col-sm-4">Single:</label>
            <div class="col-sm-8">
            <span>{{$famiglia->single()->nominativo}}</span>
            </div>
        </div>
        </li>
    </ul>
 @elseif($famiglia->capofamiglia())
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-4">Capo Famiglia:</label>
            <div class="col-sm-8">
                @if($famiglia->capofamiglia())
                    {{$famiglia->capofamiglia()->nominativo}}
                @else
                <p class="text-danger">Nessun capofamiglia</p>
                @endif
            </div>
            </div>
        </li>
        @if($famiglia->moglie())
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-4">Moglie:</label>
            <div class="col-sm-8">
                {{$famiglia->moglie()->nominativo}}
            </div>
            </div>
        </li>
        @endif
        @if(! $famiglia->figli->isEmpty())
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-2">Figli:</label>
            <div class="col-sm-10">
                <ul>
                @foreach  ($famiglia->figli as $figlio)
                    <li>
                        <div class="row">
                            <div class="col-sm-10">
                                <span> @year($figlio->data_nascita) {{$figlio->nominativo}}   ({{$figlio->pivot->posizione_famiglia}}) </span>
                            </div>
                            <div class="col-sm-2">
                                @if($figlio->pivot->stato == '1')
                                <span class="badge badge-pill badge-success">Nel nucleo</span>
                                @else
                                <span class="badge badge-pill badge-danger">Fuori da nucleo</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach    
                </ul>
            </div>
            </div>
        </li>
        @endif
    </ul>
    @endif      

