@extends("officina.index")

@section("title", "Officina Meccanica")

@section("content")
    @include("partials.header", ["title" => "Modifica Prenotazione"])
    <form
        class="container-fluid"
        id="needs-validation"
        method="POST"
        action="{{ route("officina.prenota.update", $pren->id) }}"
    >
        @csrf

        <livewire:prenotazione-veicoli
            :dataPartenza="$pren->data_partenza"
            :oraPartenza="$pren->ora_partenza"
            :dataArrivo="$pren->data_arrivo"
            :oraArrivo="$pren->ora_arrivo"
            :selectedVeicolo="$pren->veicolo_id"
        />

        <div class="row">
            <div class="col-md-3">
                <label for="cliente">Nome</label>
                <select class="form-select" id="cliente" name="nome">
                    <option value="{{ $pren->cliente_id }}" selected>
                        {{ $pren->cliente->nominativo }}
                    </option>
                    @foreach ($clienti as $cliente)
                        @if ($pren->cliente_id != $cliente->id)
                            <option value="{{ $cliente->id }}">
                                {{ $cliente->nominativo }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="meccanico">Meccanico</label>
                <select class="form-select" id="meccanico" name="meccanico">
                    <option value="{{ $pren->meccanico_id }}" selected>
                        {{ $pren->meccanico->nominativo }}
                    </option>
                    @foreach ($meccanici as $mecc)
                        @if ($pren->meccanico_id != $mecc->persona_id)
                            <option value="{{ $mecc->persona_id }}">
                                {{ $mecc->nominativo }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="uso">Uso</label>
                <select class="form-select" id="uso" name="uso">
                    <option value="{{ $pren->uso_id }}" selected>
                        {{ $pren->uso->ofus_nome }}
                    </option>
                    @foreach ($usi as $uso)
                        @if ($pren->uso_id != $uso->ofus_iden)
                            <option value="{{ $uso->ofus_iden }}">
                                {{ $uso->ofus_nome }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="destinazione">Destinazione</label>
                    <input
                        type="text"
                        class="form-control"
                        id="destinazione"
                        name="destinazione"
                        value="{{ $pren->destinazione }}"
                    />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <label for="note">Note</label>
                <input
                    type="text"
                    class="form-control"
                    id="note"
                    name="note"
                    value="{{ $pren->note }}"
                />
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button
                    type="submit"
                    id="prenota"
                    class="btn  btn-primary"
                >
                    Modifica
                </button>
            </div>
            @can("meccanica.prenotazione.elimina")
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <a
                        class="btn  btn-danger"
                        href="{{ route("officina.prenota.delete", $pren->id) }}"
                    >
                        Elimina
                    </a>
                </div>
            @endcan
        </div>
        <br />
    </form>
@endsection
