<my-modal modal-title="Aggiungi Coordinatore" button-title="Aggiungi coordinatore" button-style="btn-secondary my-2">
  <template slot="modal-body-slot">
    <form class="form" method="POST" id="formAggiungiCoord" action="{{ route('scuola.classi.coordinatore.assegna', ['id' =>$classe->id]) }}" >
      {{ csrf_field() }}
      <div class="form-group row">
        <label for="example-text-input" class="col-4 col-form-label">Coordinatore</label>
          <div class="col-8">
            <select class="form-control" name="coord_id">
              <option value="" selected>---scegli coordinatore--</option>
              @foreach ($coordPossibili as $p)
                <option value="{{ $p->id }}">{{$p->nominativo}} </option>
              @endforeach
            </select>
          </div>
      </div>
           <div class="form-group row">
        <label for="example-text-input" class="col-4 col-form-label">Data Inizio</label>
          <div class="col-8">
            <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
            <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di inzio anno scolastico.</small>
          </div>
      </div>
     </form>
  </template> 
  <template slot="modal-button">
        <button class="btn btn-danger" form="formAggiungiCoord">Salva</button>
  </template>
</my-modal> 