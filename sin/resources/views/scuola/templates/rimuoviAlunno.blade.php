<my-modal modal-title="Rimuovi Alunno" button-title="Rimuovi" button-style="btn-danger my-1">
  <template slot="modal-body-slot">
    <form class="form" method="POST" id="formRimuoviAlunno" action="{{ route('scuola.classi.alunno.rimuovi', ['id' =>$classe->id, 'alunno_id' =>$alunno->id]) }}" >
      {{ csrf_field() }}
      <div class="form-group row">
         <p> Voi davvero eliminare l'alunno {{$alunno->nominativo}} dalla {{$classe->tipo->nome}} ?</p>
      </div>
     </form>
  </template> 
  <template slot="modal-button">
        <button class="btn btn-danger" form="formRimuoviAlunno">Elimina</button>
  </template>
</my-modal> 