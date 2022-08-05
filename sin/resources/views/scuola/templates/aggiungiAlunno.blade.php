<my-modal modal-title="Aggiungi Alunno" button-title="Aggiungi Alunno" button-style="btn-primary my-2">
  <template slot="modal-body-slot">
    <form class="form" method="POST" id="formComponente" action="{{ route('scuola.classi.alunno.assegna', ['id' =>$classe->id]) }}" >
      {{ csrf_field() }}
      <div class="form-group row">
        <label for="example-text-input" class="col-4 col-form-label">Alunno</label>
          <div class="col-8">
            <select class="form-control" name="alunno_id">
              <option value="" selected>---scegli alunno--</option>
              @foreach ($possibili as $p)
                <option value="{{ $p->id }}">@year($p->data_nascita) {{$p->nominativo}} </option>
              @endforeach
            </select>
          </div>
      </div>
           <div class="form-group row">
        <label for="example-text-input" class="col-4 col-form-label">Data Inizio</label>
          <div class="col-8">
            <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
            <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se coincide con la data di inizio anno scolastico.</small>
          </div>
      </div>
     </form>
  </template> 
  <template slot="modal-button">
        <button class="btn btn-danger" form="formComponente">Salva</button>
  </template>
</my-modal> 