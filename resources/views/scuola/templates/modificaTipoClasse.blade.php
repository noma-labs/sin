<my-modal
    modal-title="Modifica Classe"
    button-title="Modifica Classe"
    button-style="btn-primary my-2"
>
    <template slot="modal-body-slot">
        <form
            class="form"
            method="POST"
            id="editClasseTipo"
            action="{{ route("scuola.classi.tipo.update", ["id" => $classe->id]) }}"
        >
            @method("PUT")
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Tipo di classe
                </label>
                <div class="col-8">
                    <select class="form-control" name="tipo_id">
                        <option value="" selected>---scegli--</option>
                        {{-- format-ignore-start --}}
                        @foreach (App\Scuola\Models\ClasseTipo::orderBy("ord")->orderBy("nome")->get() as $t)
                            @if ($classe->tipo->id == $t->id)
                                <option value="{{ $t->id }}" selected>
                                    {{ $t->nome }} ({{ $t->ciclo }})
                                </option>
                            @else
                                <option value="{{ $t->id }}">
                                    {{ $t->nome }} ({{ $t->ciclo }})
                                </option>
                            @endif
                        @endforeach
                        {{-- format-ignore-end --}}
                    </select>
                </div>
            </div>
        </form>
    </template>
    <template slot="modal-button">
        <button class="btn btn-danger" form="editClasseTipo">Modifica</button>
    </template>
</my-modal>
