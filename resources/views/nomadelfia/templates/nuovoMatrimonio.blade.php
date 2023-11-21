<my-modal modal-title="Nuovo Matrmonio" button-title="Matrimonio" button-style="btn-warning my-2">
    <template slot="modal-body-slot">
        <form class="form" method="POST" id="formAggiornaFamiglia{{$famiglia->id}}" action="">
            {{ csrf_field() }}
               <div class="form-group row">
                   <label for="example-text-input" class="col-4 col-form-label">Moglie</label>
                   <div class="col-8">
                       <autocomplete placeholder="Inserisci nominativo..." name="persona_id"
                                     url="{{route('api.nomadeflia.popolazione.search')}}"></autocomplete>
                   </div>
               </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger" form="formAggiornaFamiglia{{$famiglia->id}}">Salva</button>
    </template>
</my-modal>
