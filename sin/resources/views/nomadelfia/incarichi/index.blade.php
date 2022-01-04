@extends('nomadelfia.index')

@section('archivio')

    @include('partials.header', ['title' => 'Gestione Incarichi'])

    @include("nomadelfia.templates.aggiungiIncarico")

    <div class="row">
        <div class="col-md-9">
            @foreach ($incarichi->chunk(4) as $chunk)
                <div class="row my-2">
                    @foreach ($chunk as $incarico)
                        <div class="col-md-3">
                            <div id="accordion">
                                <div class="card">
                                    <div class="card-header" id="heading{{$incarico->id}}">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" data-toggle="collapse"
                                                    data-target="#collapse{{$incarico->id}}" aria-expanded="true"
                                                    aria-controls="collapse{{$incarico->id}}">
                                                {{ $incarico->nome }}
                                                <span class="badge badge-primary badge-pill">{{ $incarico->lavoratoriAttuali->count() }}</span>
                                            </button>
                                        </h5>
                                    </div>

                                    <div id="collapse{{$incarico->id}}" class="collapse"
                                         aria-labelledby="heading{{$incarico->id}}" data-parent="#accordion">
                                        <div class="card-body">
                                            <ul>
                                                @foreach($incarico->lavoratoriAttuali as $lavoratore)
                                                    <li>@include('nomadelfia.templates.persona', ['persona'=>$lavoratore])</li>
                                                @endforeach
                                            </ul>
                                            <div class="row">
                                                <div class="col-sm">
                                                    <a class="btn btn-secondary btn-block col-md-4 offset-md-2" type="button"
                                                       href="{{ route('nomadelfia.incarichi.edit', $incarico->id)}}">Modifica</a>
                                                </div>
                                                <div class="col-sm">
                                                    @include('nomadelfia.templates.eliminaIncarico', ['incarico'=>$incarico])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>  <!-- end card -->
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Suggerimenti</h3>
                    <b>Queste persone hanno {{$min}} o più incarichi:</b>
                    <ul class="list-group list-group-flush">
                        @forelse($busy as $b)
                            <li class="list-group-item"> @include('nomadelfia.templates.persona', ['persona'=>$b])  <span class="badge badge-primary">{{$b->count}}</span> </li>
                        @empty
                            <p>Nessuna persona</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
