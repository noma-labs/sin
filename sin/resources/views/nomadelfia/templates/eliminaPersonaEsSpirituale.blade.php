<my-modal modal-title="Elimina persona da es.spirituale" button-title="Elimina" button-style="btn-danger my-2">
    <template slot="modal-body-slot">
        <form class="form" method="POST"  id="formEliminaFromEsSpirituale{{$posizione->id}}" action="{{ route('nomadelfia.persone.posizione.elimina', ['idPersona' =>$persona->id, 'id'=>$posizione->id]) }}" >      
            @csrf
            @method('delete')
            <body> Vuoi davvero eliminare {{$persona->nominativo}} dalla posizione  {{$posizione->nome}} ? </body>
        </form>
    </template> 
    <template slot="modal-button">
        <button class="btn btn-danger" form="formEliminaFromEsSpirituale{{$posizione->id}}" >Elimina</button>
    </template>
</my-modal> 