<my-modal modal-title="Modifica Posizione attuale" button-title="Modifica" button-style="btn-warning my-2">
    <template slot="modal-body-slot">
    <form class="form" method="POST"  id="formPersonaPosizioneModifica{{$persona->id}}" action="{{ route('nomadelfia.persone.posizione.modifica', ['idPersona' =>$persona->id, 'id'=>$id]) }}" >      
            @method("PUT")
            {{ csrf_field() }}
            <input type="hidden" name="current_data_inizio"  value="{{$data_inizio}}"  />
            <div class="form-group row">
                <label class="col-sm-6 col-form-label"> Data Attuale</label>
                <div class="form-check  col-sm-6 ">
                        <label class="form-check-label" for="exampleRadios1">
                                {{$data_inizio}}
                        </label>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-6 col-form-label">Nuova Data</label>
                <div class="form-check  col-sm-6">
                        <label class="form-check-label">
                                <date-picker :bootstrap-styling="true" :typeable="true" value="{{$persona->data_nascita }}" format="yyyy-MM-dd" name="new_data_inizio"></date-picker>
                        </label>
                </div>
            </div>
        </form>
    </template> 

    <template slot="modal-button">
        <button class="btn btn-success" form="formPersonaPosizioneModifica{{$persona->id}}">Salva</button>
    </template>
</my-modal> <!--end modal modifica posizione-->