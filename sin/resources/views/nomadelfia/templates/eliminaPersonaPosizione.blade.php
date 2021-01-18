<my-modal modal-title="Elimina posizione della persona" button-title="Elimina" button-style="btn-danger my-2">
    <template slot="modal-body-slot">
        <form class="form" method="POST"  id="formEliminaposizioneStorico{{$persona->id}}" action="{{ route('nomadelfia.esercizi.elimina', ['id'=>$esercizio->id, 'idPersona' =>$persona->id]) }}" >      
            @csrf
            @method('delete')
            <body> Vuoi davvero eliminare {{$persona->nominativo}} dal turno  {{$esercizio->turno}} ? </body>
        </form>
    </template> 
    <template slot="modal-button">
        <button class="btn btn-danger" form="formEliminaposizioneStorico{{$persona->id}}" >Elimina</button>
    </template>
</my-modal> 