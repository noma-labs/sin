@if($famiglia->componenti->isEmpty())
    <p class="text-danger">Nessun componente</p>
@else
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
                <label class="col-sm-4">Figli:</label>
                <div class="col-sm-8">
                    @foreach  ($famiglia->figli as $figlio)
                    <div class="row">
                        <div class="col-sm-5">
                            <span> @year($figlio->data_nascita) {{$figlio->nominativo}}   ({{$figlio->pivot->posizione_famiglia}}) </span>
                        </div>
                        <div class="col-sm-4">
                            @if($figlio->pivot->stato == '1')
                            <span class="badge badge-pill badge-success">Nel nucleo</span>
                            @else
                            <span class="badge badge-pill badge-danger">Fuori da nucleo</span>
                            @endif
                        </div>
                        <div class="col-sm-3">
                            <my-modal modal-title="Aggiorna componente" button-title="Modifica" button-style="btn-warning my-2">
                                <template slot="modal-body-slot">
                                <form class="form" method="POST" id="formComponenteAggiorna" action="{{ route('nomadelfia.famiglie.componente.aggiorna', ['id' =>$famiglia->id]) }}" >      
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label">Persona</label>
                                        <div class="col-8" >
                                            <input class="form-control" type="text" name="persona_id" value="{{$figlio->id}}" hidden>
                                            {{$figlio->nominativo}}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label">Posizione Famiglia</label>
                                        <div class="col-8">
                                        <select class="form-control" name="posizione">
                                        <option value="" selected>---scegli posizione---</option>
                                            @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                                                @if($posizione == $figlio->pivot->posizione_famiglia)
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
                                                <date-picker :bootstrap-styling="true" value="{{$figlio->pivot->data_entrata}}" format="yyyy-MM-dd" name="data_entrata"></date-picker>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label">Data uscita dalla famiglia:</label>
                                        <div class="col-8">
                                        <date-picker :bootstrap-styling="true" value="{{$figlio->pivot->data_uscita}}" format="yyyy-MM-dd" name="data_uscita"></date-picker>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                    <label for="example-text-input" class="col-4 col-form-label">Stato:</label>
                                        <div class="col-8">
                                        <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stato" id="stato1" value="1" {{ $figlio->pivot->stato === '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="stato1">
                                            Includi nel nucleo familiare
                                        </label>
                                        </div>
                                        <div class="form-check">
                                        <input class="form-check-input" type="radio" name="stato" id="stato2" value="0" {{ $figlio->pivot->stato === '0' ? 'checked' : '' }}>
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
                                    <button class="btn btn-danger" form="formComponenteAggiorna">Salva</button>
                                </template>
                            </my-modal>  
                        </div>
                    </div>
                    @endforeach    
                </div>
                </div>
            </li>
            @endif
        </ul>
        @endif      
@endif