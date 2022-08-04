@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('archivio')
    @include('partials.header', ['title' => "Anno scolastico " . $anno->scolastico])

<div class="row">
    <div class="col-md-12">
        <div class="card-deck">
            <div class="card">
                <div class="card-header">
                    Scuola A.S.  {{$anno->scolastico}}
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush ">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <p>Responsabile Scuola</p>
                            @if ($resp)
                                @include("nomadelfia.templates.persona", ['persona' => $resp])
                            @else
                                Non Assegnato
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Studenti
                            <span class="badge badge-primary badge-pill">{{$alunni}} </span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer">

                </div>
            </div>
        </div>
    </div>
</div>
@include('scuola.templates.aggiungiClasse',["anno"=>$anno])


    <form class="form" method="POST" id="importStudents" action="{{  route('scuola.anno.import', ["id"=>$anno->id]) }}" >
        {{ csrf_field() }}
        <div class="form-group row">
            <label for="example-text-input" class="col-4 col-form-label">Tipo di classe</label>
            <div class="col-8">
                <select class="form-control" name="anno_from">
                    <option value="" selected>---scegli--</option>
                    @foreach (App\Scuola\Models\Anno::orderBy("scolastico")->get() as  $t)
                        <option value="{{ $t->id }}"> {{$t->scolastico}} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <button class="btn btn-danger" form="importStudents">Aggiungi</button>
    </form>

@foreach ($classi->chunk(3) as $chunk)
    <div class="row my-2">
        @foreach ($chunk as $classe)
            <div class="col-md-4">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="heading{{$classe->id}}">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse"
                                        data-target="#collapse{{$classe->id}}" aria-expanded="true"
                                        aria-controls="collapse{{$classe->id}}">
                                    {{ $classe->tipo->nome }}
                                    <span class="badge badge-primary badge-pill">{{ $classe->alunni()->count() }}</span>
                                </button>
                            </h5>
                        </div>
                        <div id="collapse{{$classe->id}}" class="collapse" aria-labelledby="heading{{$classe->id}}"
                             data-parent="#accordion">
                            <div class="card-body">
                                <div>Alunni</div>
                                <ul>
                                    @foreach($classe->alunni as $alunno)
                                        <li>
                                            @year($alunno->data_nascita) @include('nomadelfia.templates.persona', ['persona'=>$alunno])
                                            @liveRome($alunno)
                                            <span class="badge badge-warning">Roma</span>
                                            @endliveRome
                                        </li>
                                    @endforeach
                                </ul>
                                <a class="btn btn-primary" href="{{ route('scuola.classi.show',$classe->id)}}">Dettaglio</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
@endsection
