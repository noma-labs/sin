@if($famiglia->componenti->isEmpty())
    <p class="text-danger">Nessun componente</p>
@elseif($famiglia->capofamiglia())
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-2">Capo Famiglia:</label>
            <div class="col-sm-8">
                @if($famiglia->capofamiglia())
                   @include("nomadelfia.templates.persona", ['persona' => $famiglia->capofamiglia()]) 
                @else
                <p class="text-danger">Nessun capofamiglia</p>
                @endif
            </div>
             <div class="col-sm-2">
                    @include("nomadelfia.templates.aggiornaComponente", ['componente' => $famiglia->capofamiglia()]) 
                </div> 
            </div>
        </li>
        @if($famiglia->moglie())
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-2">Moglie:</label>
            <div class="col-sm-8">
                    @include("nomadelfia.templates.persona", ['persona' => $famiglia->moglie()]) 

            </div>
            <div class="col-sm-2">
                    @include("nomadelfia.templates.aggiornaComponente", ['componente' =>  $famiglia->moglie()]) 
            </div>
            </div>
        </li>
        @endif
        @if(! $famiglia->figli->isEmpty())
        <li class="list-group-item">
            <div class="row">
            <label class="col-sm-2">Figli:</label>
            <div class="col-sm-10">
                @foreach  ($famiglia->figli as $figlio)
                <div class="row justify-content-between">
                    <div class="col-md-6">
                        <span> @year($figlio->data_nascita)   @include("nomadelfia.templates.persona", ['persona' => $figlio])   ({{$figlio->pivot->posizione_famiglia}}) </span>
                        @if($figlio->pivot->stato == '1')
                        <span class="badge badge-pill badge-success">Nel nucleo</span>
                        @else
                        <span class="badge badge-pill badge-danger">Fuori da nucleo</span>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @include("nomadelfia.templates.aggiornaComponente", ['componente' => $figlio]) 
                    </div>
                </div>
                @endforeach    
            </div>
            </div>
        </li>
        @endif
    </ul>   
    @include("nomadelfia.templates.aggiungiComponente", ['famiglia' => $famiglia])
@endif