<form
    class="form"
    method="POST"
    action="{{ route("nomadelfia.join.store", $persona->id) }}"
>
    @csrf

    <livewire:entrata-persona :persona="$persona" />

    <div class="row my-2">
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Salva</button>
        </div>
    </div>
</form>
