
@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi alunni a elaborato"])


    <livewire:filter-alunno />

    <h1>{{$anno->as}}</h1>


    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="form-group">
                <label for="{{ $anno->prescuola->ciclo }}">{{ $anno->prescuola->ciclo  }}</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $anno->prescuola->ciclo  }}" wire:model="selectedCicli">
                    <label class="form-check-label">
                        {{ $anno->prescuola->ciclo  }}
                    </label>
                </div>
                <div class="ml-4">
                    @foreach ($anno->prescuola->classi as $classe)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="{{ $classe->nome }}" wire:model="selectedClassi">
                        <label class="form-check-label">
                            Classe: {{ $classe->nome }}
                        </label>
                    </div>
                        <ul>
                            @foreach ($classe->alunni as $alunno)
                                <li>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $alunno->id }}" wire:model="selectedStudents">
                                        <label class="form-check-label">
                                            {{ $alunno->nominativo }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
