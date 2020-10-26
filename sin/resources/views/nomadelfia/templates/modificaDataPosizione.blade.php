<my-modal modal-title="Modifica Posizione attuale" button-title="Modifica" button-style="btn-warning my-2">
    <template slot="modal-body-slot">
    <form class="form" method="POST"  id="formPersonaPosizioneModifica{{$persona->id}}" action="{{ route('nomadelfia.persone.posizione.modifica', ['idPersona' =>$persona->id, 'id'=>$id]) }}" >      
            {{ csrf_field() }}
            <input type="hidden" name="current_data_inizio"  value="{{$data_inizio}}"  />
            <div class="form-group row">
            <label class="col-sm-6 col-form-label">Data inizio</label>
            <div class="col-sm-6">
                <date-picker :bootstrap-styling="true" value="{{$data_inizio }}" format="yyyy-MM-dd" name="new_data_inizio"></date-picker>
            </div>
            </div>
        </form>
    </template> 

    <template slot="modal-button">
        <button class="btn btn-success" form="formPersonaPosizioneModifica{{$persona->id}}">Salva</button>
    </template>
</my-modal> <!--end modal modifica posizione-->