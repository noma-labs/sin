@extends('scuola.index')

@section('title', 'Gestione Scuola')

@section('archivio')
    <my-modal modal-title="Aggiungi A.Scolastico" button-title="Aggiungi Anno Scolastico" button-style="btn-primary my-2">
        <template slot="modal-body-slot">
            <form class="form" method="POST" id="formComponente" action="{{ route('nomadelfia.incarichi.aggiungi') }}" >
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="annoInizio">Anno</label>
                    <input type="text" name="name" class="form-control" id="annoInizio" aria-describedby="emailHelp" placeholder="Anno Inizio">
                </div>
            </form>
        </template>
        <template slot="modal-button">
            <button class="btn btn-danger" form="formComponente">Salva</button>
        </template>
    </my-modal>

    @foreach(collect($anni)->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $anno)
            <div class="col-md-4 my-1">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="heading{{$anno->id}}">
                            <h5 class="mb-0">
                                {{$anno->scolastico}}
                                <a class="btn btn-link" href="{{ route('scuola.anno', $anno->id)}}">{{$anno->scolastico}}</a>
                                <span class="badge badge-primary badge-pill"></span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endforeach
@endsection
