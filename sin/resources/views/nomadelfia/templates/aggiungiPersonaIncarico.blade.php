<my-modal modal-title="Aggiungi Persona incarico" button-title="Aggiungi Persona" button-style="btn-primary my-2">
  <template slot="modal-body-slot">
    <form class="form" method="POST" id="formAssegnaIncaricoPersona" action="{{ route('nomadelfia.incarichi.assegna', ["id"=>$incarico->id]) }}" >
      {{ csrf_field() }}
      <div class="form-group row">
        <label for="example-text-input" class="col-4 col-form-label">Persona</label>
        <div class="col-8">
          <select class="form-control" name="persona_id">
            <option value="" selected>---scegli persona--</option>
            @foreach ($possibili as $p)
              <option value="{{ $p->id }}">{{$p->nominativo}} </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="example-text-input" class="col-4 col-form-label">Data Inizio</label>
        <div class="col-8">
          <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') }}" format="yyyy-MM-dd" name="data_inizio"></date-picker>
          <small id="emailHelp" class="form-text text-muted">Lasciare vuoto se concide con la data di oggi.</small>
        </div>
      </div>
    </form>
  </template>
  <template slot="modal-button">
        <button class="btn btn-danger" form="formAssegnaIncaricoPersona">Salva</button>
  </template>
</my-modal>