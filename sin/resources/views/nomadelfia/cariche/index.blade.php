@extends('nomadelfia.index')

@section('archivio')

@include('partials.header', ['title' => 'Gestione Cariche costituzionali'])
<div class="row ">
    <div class="col-md-4 card-deck">
        <div class="card" >
            <div class="card-header">
               Associazione Nomadelfia
            </div>
            <div class="card-body">
                <ul>
                    @foreach($ass as $key => $membri)
                        <li>{{$key}} </li>
                        <ul>
                            @foreach($membri as $m)
                                <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li>
                            @endforeach
                        </ul>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Modifica</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 card-deck">
        <div class="card" >
            <div class="card-header">
                Solidarietà Nomadelfia ODV
            </div>
            <div class="card-body">
                <ul>
                    @foreach($sol as $key => $membri)
                        <li>{{$key}} </li>
                        <ul>
                            @foreach($membri as $m)
                                <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li>
                            @endforeach
                        </ul>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Modifica</a>
            </div>
        </div>
    </div>
    <div class="col-md-4 card-deck">
        <div class="card" >
            <div class="card-header">
                Fondazione Nomadelfia
            </div>
            <div class="card-body">
                <ul>
                    @foreach($fon as $key => $membri)
                        <li>{{$key}} </li>
                        <ul>
                            @foreach($membri as $m)
                                <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li>
                            @endforeach
                        </ul>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Modifica</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-4 card-deck">
        <div class="card" >
            <div class="card-header">
                Cooperativa Agricola Culturale
            </div>
            <div class="card-body">
                <ul>
                    @foreach($agr as $key => $membri)
                        <li>{{$key}} </li>
                        <ul>
                            @foreach($membri as $m)
                                <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li>
                            @endforeach
                        </ul>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Modifica</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 card-deck">
        <div class="card" >
            <div class="card-header">
                Cooperativa Culturale
            </div>
            <div class="card-body">
                <ul>
                    @foreach($cul as $key => $membri)
                        <li>{{$key}} </li>
                        <ul>
                            @foreach($membri as $m)
                                <li>@include("nomadelfia.templates.persona", ['persona' => $m])  </li>
                            @endforeach
                        </ul>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Modifica</a>
            </div>
        </div>
    </div>
</div>
@endsection
