@extends("agraria.index")

@section("title", "Dettaglio Manutenzione")

@section("content")
    <div class="container mt-4">
        <div class="card card-mod">
            <div class="card-header">
                <h3 class="card-title mb-0">Dettaglio Manutenzione</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Mezzo Agricolo</dt>
                    <dd class="col-sm-9">
                        {{ $manutenzione->mezzo->nome ?? "-" }}
                    </dd>

                    <dt class="col-sm-3">Data</dt>
                    <dd class="col-sm-9">{{ $manutenzione->data }}</dd>

                    <dt class="col-sm-3">Ore</dt>
                    <dd class="col-sm-9">{{ $manutenzione->ore }}</dd>

                    <dt class="col-sm-3">Spesa</dt>
                    <dd class="col-sm-9">
                        €
                        {{ number_format($manutenzione->spesa, 2, ",", ".") }}
                    </dd>

                    <dt class="col-sm-3">Persona</dt>
                    <dd class="col-sm-9">{{ $manutenzione->persona }}</dd>

                    <dt class="col-sm-3">Lavori Extra</dt>
                    <dd class="col-sm-9">
                        {{ $manutenzione->lavori_extra ?? "-" }}
                    </dd>

                    <dt class="col-sm-3">Manutenzioni Programmate</dt>
                    <dd class="col-sm-9">
                        @if ($manutenzione->programmate && $manutenzione->programmate->count())
                            <ul class="mb-0 ps-3">
                                @foreach ($manutenzione->programmate as $programmata)
                                    <li>
                                        {{ $programmata->nome ?? "" }}
                                        @if (isset($programmata->pivot->data))
                                                ({{ $programmata->pivot->data }})
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </dd>
                </dl>
                <a
                    href="{{ route("agraria.maintenanace.edit", $manutenzione->id) }}"
                    class="btn btn-secondary mt-3"
                >
                    Modifica
                </a>
                <form method="POST" action="{{ route('agraria.maintenanace.destroy', $manutenzione->id) }}" class="d-inline ms-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mt-3" onclick="return confirm('Sei sicuro di voler eliminare questa manutenzione?')">
                        Elimina
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
