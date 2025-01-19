<x-modal
    modal-title="Aggiungi Classe"
    button-title="Aggiungi Classe"
    button-style="btn-primary my-2"
>
    <x-slot:body>
        <form
            class="form"
            method="POST"
            id="addClasse"
            action="{{ route("scuola.anno.classe.aggiungi", ["id" => $anno->id]) }}"
        >
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="example-text-input" class="col-4 col-form-label">
                    Tipo di classe
                </label>
                <div class="col-8">
                    <select class="form-control" name="tipo">
                        <option value="" selected>---scegli--</option>
                        {{-- format-ignore-start --}}
                        @foreach (App\Scuola\Models\ClasseTipo::orderBy("ord")->orderBy("nome")->get() as $t)
                            <!-- prettier-ignore-end -->
                            <option value="{{ $t->id }}">
                                {{ $t->nome }} ({{ $t->ciclo }})
                            </option>
                        @endforeach
                        {{-- format-ignore-end --}}
                    </select>
                </div>
            </div>
        </form>
    </x-slot>
    <x-slot:footer>
        <button class="btn btn-danger" form="addClasse">Aggiungi</button>
    </x-slot>
</x-modal>
