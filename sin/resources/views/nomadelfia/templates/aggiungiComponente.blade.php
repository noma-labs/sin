<my-modal modal-title="Aggiungi componente alla famiglia" button-title="Aggiungi Componente" button-style="btn-primary my-2">
        <template slot="modal-body-slot">
          <form class="form" method="POST" id="formComponente" action="{{ route('nomadelfia.famiglie.componente.assegna', ['id' =>$famiglia->id]) }}" >      
            {{ csrf_field() }}
            <div class="form-group row">
              <label for="example-text-input" class="col-4 col-form-label">Persona</label>
                <div class="col-8">
                  <autocomplete placeholder="Inserisci nominativo..." name="persona_id" url="{{route('api.nomadeflia.persone.search')}}"></autocomplete>
                </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-4 col-form-label">Posizione Famiglia</label>
                <div class="col-8">
                  <select class="form-control" name="posizione">
                  <option value="" selected>---scegli posizione---</option>
                    @foreach (App\Nomadelfia\Models\Famiglia::getEnum('Posizione') as $posizione)
                        <option value="{{ $posizione }}">{{ $posizione }}</option>
                      @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-4 col-form-label">Data entrata nella famiglia:</label>
                <div class="col-8">
                  <date-picker :bootstrap-styling="true" value="{{ old('data_entrata') }}" format="yyyy-MM-dd" name="data_entrata"></date-picker>
                  <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di nascita del nuovo componente.</small>
                </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-4 col-form-label">Stato:</label>
                <div class="col-8">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="stato" id="stato1" value="1" checked>
                  <label class="form-check-label" for="stato1">
                    Includi nel nucleo familiare
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="stato" id="stato2" value="0">
                  <label class="form-check-label" for="stato2">
                    Non includere nel nucleo familiare
                  </label>
                </div>
                </div>
            </div>
            <div class="form-group row">
              <label for="example-text-input" class="col-4 col-form-label">Note:</label>
                <div class="col-8">
                  <!-- <input type="date" class="form-control" name="note" placeholder="Data entrata nella famiglia" > -->
                  <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>
            </div>
          </form>
        </template> 
        <template slot="modal-button">
              <button class="btn btn-danger" form="formComponente">Salva</button>
        </template>
      </my-modal> 