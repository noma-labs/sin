
@if($famiglia->single())
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
        <div class="row">
            <label class="col-sm-4">Single:</label>
            <div class="col-sm-8">
            <span> @include("nomadelfia.templates.persona", ['persona' => $famiglia->single()])</span>
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
                    @include("nomadelfia.templates.persona", ['persona' => $famiglia->capofamiglia()])
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
                @include("nomadelfia.templates.persona", ['persona' => $famiglia->moglie()])
            </div>
            </div>
        </li>
        @endif
        @if(! $famiglia->figliAttuali->isEmpty())
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-4">Figli:</label>
            <div class="col-sm-8">
                @foreach  ($famiglia->figliAttuali as $figlio)
                <div class="row">
                    <div class="col-sm-12">
                        <span> @year($figlio->data_nascita)  
                        @include("nomadelfia.templates.persona", ['persona' => $figlio])
                        ({{$figlio->pivot->posizione_famiglia}}) </span>
                    </div>
                    <div class="col-sm-2">
                        
                    </div>
                </div>
                @endforeach    
            </div>
            </div>
        </li>
        @endif
    </ul>
    @endif      

