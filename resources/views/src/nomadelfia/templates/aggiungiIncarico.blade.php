<x-modal
    modal-title="Aggiungi incarico"
    button-title="Aggiungi incarico"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="formComponente"
            action="{{ route("nomadelfia.incarichi.aggiungi") }}"
        >
            @csrf
            <div class="mb-3">
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
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="formComponente">Salva</button>
    </x-slot>
</x-modal>
