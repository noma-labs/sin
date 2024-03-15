<my-modal
    modal-title="Aggiungi incarico"
    button-title="Aggiungi incarico"
    button-style="btn-primary my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="formComponente"
            action="{{ route("nomadelfia.incarichi.aggiungi") }}"
        >
            {{ csrf_field() }}
            <div class="form-group">
                <label for="exampleInputEmail1">Nome Incarico</label>
                <input
                    type="text"
                    name="name"
                    class="form-control"
                    id="exampleInputEmail1"
                    aria-describedby="emailHelp"
                    placeholder="Nome Incarico"
                />
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger" form="formComponente">Salva</button>
    </template>
</my-modal>
