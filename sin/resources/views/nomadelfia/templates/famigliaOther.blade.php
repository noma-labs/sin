
<ul class="list-group list-group-flush">
    @forelse  ($famiglia->componenti as $componente)
        <div class="row">
            <label class="col-sm-2"> {{$componente->pivot->posizione_famiglia}} :</label>
            <div class="col-md-6">
                @include("nomadelfia.templates.persona", ['persona' => $componente]) 
            </div>
            <div class="col-md-2">
                    @include("nomadelfia.templates.aggiornaComponente", ['componente' => $componente]) 
            </div>
        </div>
    @empty
            <p class="text-danger">Nessun componente nella famiglia single</p>
    @endforelse    
   @include("nomadelfia.templates.aggiungiComponente", ['famiglia' => $famiglia])
</ul>