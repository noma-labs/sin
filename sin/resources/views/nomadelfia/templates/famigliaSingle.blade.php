
<ul class="list-group list-group-flush">
        @forelse  ($famiglia->componenti as $componente)
            <div class="row">
                <label class="col-sm-2"> {{$componente->pivot->posizione_famiglia}} :</label>
                <div class="col-md-6">
                    @include("nomadelfia.templates.persona", ['persona' => $componente]) 
                    @if($componente->pivot->stato == '1')
                    <span class="badge badge-pill badge-success">Nel nucleo</span>
                    @else
                    <span class="badge badge-pill badge-danger">Fuori da nucleo</span>
                    @endif
                </div>
                <div class="col-md-2">
                    <my-modal modal-title="Aggiorna componente" button-title="Modifica" button-style="btn-warning my-2">
                            <template slot="modal-body-slot">
                            <form class="form" method="POST" id="formComponenteAggiorna{{$componente->id}}" action="{{ route('nomadelfia.famiglie.componente.aggiorna', ['id' =>$famiglia->id]) }}" >      
                                {{ csrf_field() }}
                                <div class="form-group row">
                                <label for="example-text-input" class="col-4 col-form-label">Persona</label>
                                    <div class="col-8" >
            
                                        <input class="form-control" type="text" name="persona_id" value="{{$componente->id}}" hidden>
                                        {{$componente->nominativo}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label for="example-text-input" class="col-4 col-form-label">Posizione Famiglia</label>
                                    <div class="col-8">
                                    <select class="form-control" name="posizione">
                                    <option value={{ $componente->pivot->posizione_famiglia }} selected>---scegli posizione---</option>
                                        @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                                            @if($posizione == $componente->pivot->posizione_famiglia)
                                            <option value="{{ $posizione }}" selected>{{ $posizione }}</option>
                                            @else
                                            <option value="{{ $posizione }}">{{ $posizione }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label for="example-text-input" class="col-4 col-form-label">Data entrata nella famiglia: 
                                </label>
                                    <div class="col-8">
                                            <date-picker :bootstrap-styling="true" value="{{$componente->pivot->data_entrata}}" format="yyyy-MM-dd" name="data_entrata"></date-picker>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label for="example-text-input" class="col-4 col-form-label">Data uscita dalla famiglia:</label>
                                    <div class="col-8">
                                    <date-picker :bootstrap-styling="true" value="{{$componente->pivot->data_uscita}}" format="yyyy-MM-dd" name="data_uscita"></date-picker>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label for="example-text-input" class="col-4 col-form-label">Stato:</label>
                                    <div class="col-8">
                                    <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stato" id="stato1" value="1" {{ $componente->pivot->stato === '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stato1">
                                        Includi nel nucleo familiare
                                    </label>
                                    </div>
                                    <div class="form-check">
                                    <input class="form-check-input" type="radio" name="stato" id="stato2" value="0" {{ $componente->pivot->stato === '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="stato2">
                                        Non includere nel nucleo familiare
                                    </label>
                                    </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label for="example-text-input" class="col-4 col-form-label">Note:</label>
                                    <div class="col-8">
                                    <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"></textarea>
                                    </div>
                                </div>
                            </form>
                            </template> 
                            <template slot="modal-button">
                                <button class="btn btn-danger" form="formComponenteAggiorna{{$componente->id}}">Salva</button>
                            </template>
                        </my-modal>  
                </div>
            </div>
        @empty
             <p class="text-danger">Nessun componente nelal famiglia single</p>
        @endforelse    
</ul>