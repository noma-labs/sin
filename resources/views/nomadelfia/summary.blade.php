@extends('nomadelfia.index')

@section('title', 'Gestione Nomadelfia')


@section('archivio')

    <div class="card-deck">
        <div class="card ">
            <div class="card-header">
                Gestione Popolazione
            </div>
            <div class="card-body">
                <div class="row justify-content-between">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush ">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione')}}"> Popolazione Nomadelfia</a>
                                <span class="badge badge-primary badge-pill"> {{$totale}}</span>
                            </li>
                            <li class="list-group-item  d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.maggiorenni')}}"> Maggiorenni</a>
                                <p>Donne ({{count($maggiorenni->donne)}})</p>
                                <p>Uomini ({{count($maggiorenni->uomini)}})</p>
                                <span class="badge badge-primary badge-pill"> {{$maggiorenni->total}}</span>
                            </li>
                            <li class="list-group-item  d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.posizione.effettivi')}}"> Effettivi</a>
                                <p>Donne ({{count($effettivi->donne)}})</p>
                                <p>Uomini ({{count($effettivi->uomini)}})</p>
                                <span class="badge badge-primary badge-pill"> {{$effettivi->total}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.posizione.postulanti')}}"> Postulanti</a>
                                <p>Donne ({{count($postulanti->donne)}})</p>
                                <p>Uomini ({{count($postulanti->uomini)}})</p>
                                <span class="badge badge-primary badge-pill"> {{$postulanti->total}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.posizione.ospiti')}}"> Ospiti</a>
                                <p>Donne ({{count($ospiti->donne)}})</p>
                                <p>Uomini ({{count($ospiti->uomini)}})</p>
                                <span class="badge badge-primary badge-pill"> {{$ospiti->total}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.stati.sacerdoti')}}"> Sacerdoti</a>
                                <span class="badge badge-primary badge-pill"> {{count($sacerdoti)}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.stati.mammevocazione')}}"> Mamme di
                                    Vocazione</a>
                                <span class="badge badge-primary badge-pill"> {{count($mvocazione)}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.stati.nomadelfamamma')}}"> Nomadelfa Mamma</a>
                                <span class="badge badge-primary badge-pill"> {{count($nomanamma)}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.posizione.figli.maggiorenni')}}"> Figli
                                    Maggiorenni</a>
                                <p>Donne ({{count($figliMaggiorenni->donne)}})</p>
                                <p>Uomini ({{count($figliMaggiorenni->uomini)}})</p>
                                <span class="badge badge-primary badge-pill"> {{$figliMaggiorenni->total}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{route('nomadelfia.popolazione.posizione.figli.minorenni')}}"> Figli
                                    Minorenni</a>
                                <p>Donne ({{count($minorenni->donne)}})</p>
                                <p>Uomini ({{count($minorenni->uomini)}})</p>
                                <span class="badge badge-primary badge-pill"> {{$minorenni->total}}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <p> Statistiche</p>
                        <ul class="list-group list-group-flush ">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p> Eta massima</p>
                                <span class="badge badge-primary badge-pill"> {{$stats->max}} </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p> Eta media</p>
                                <span class="badge badge-primary badge-pill"> {{$stats->avg}}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p> Eta minima</p>
                                <span class="badge badge-primary badge-pill"> {{$stats->min}} </span>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>


            <div class="card-footer">
                <a href="{{ route('nomadelfia.popolazione') }}" class=" text-center  btn btn-primary">Entra</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Gestione Famiglie
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label>Famiglie per posizione</label>
                        <ul>
                            @foreach ($posizioniFamiglia as $posizione)
                                <li>{{$posizione->posizione_famiglia}} : <strong>  {{$posizione->count}}</strong>
                                    @if($posizione->sesso == 'F')
                                        <span class="badge badge-primary"> {{$posizione->sesso}}</span>
                                    @else
                                        <span class="badge badge-warning"> {{$posizione->sesso}}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="col-md-6">
                        <label>Famiglie Numerose</label>
                        <ul>
                            @foreach ($famiglieNumerose as $fam)
                                <li>
                                    <a href="{{route('nomadelfia.famiglia.dettaglio',['id'=>$fam->id])}}"> {{$fam->nome_famiglia}}</a>
                                    <span class="badge badge-primary"> {{$fam->componenti}}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.famiglie') }}" class="btn btn-primary">Entra</a>
            </div>
        </div>

        <div class="card ">
            <div class="card-header">
                Gestione Gruppi Familiari
            </div>
            <div class="card-body">
                <ul>
                    @foreach ($gruppi as $gruppo)
                        <li>
                            @include('nomadelfia.templates.gruppo', ['id'=>$gruppo->id, "nome"=>$gruppo->nome])
                            <span class="badge badge-primary badge-pill"> {{$gruppo->count}}</span>
                            </strong>

                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer">

                <a href="{{ route('nomadelfia.gruppifamiliari') }}" class="btn btn-primary">Entra </a>
            </div>
        </div>
    </div> <!-- end card deck-->
    <div class="card-deck my-2">

        <div class="card ">
            <div class="card-header">
                Gestione Aziende
            </div>
            <div class="card-body">
            </div>
            <div class="card-footer">
                <a href="{{ route('nomadelfia.aziende') }}" class="btn btn-primary">Entra</a>
            </div>
        </div>

        <div class="card ">
            <div class="card-header">
                Scuola Familiare
            </div>
            <div class="card-body">
                <p class="card-text">
                </p>
            </div>
            <div class="card-footer">
                <a href="{{ route('scuola.summary') }}" class="btn btn-primary">Entra</a>
            </div>
        </div>
    </div> <!-- end card deck-->

    <!-- <a href="{{ route('nomadelfia.popolazione.stampa') }}" class="btn btn-info my-2">Stampa Popolazione</a>  -->
    <!-- <a href="{{ route('nomadelfia.popolazione.anteprima') }}" class="btn btn-info my-2">Anteprima stampa</a>  -->

    <my-modal modal-title="Stampa elenchi" button-title="Stampa Popolazione Nomadelfia" button-style="btn-success my-2">
        <template slot="modal-body-slot">
            <form class="form" method="POST" id="formStampa" action="{{ route('nomadelfia.popolazione.stampa') }}">
                {{ csrf_field() }}
                <h5>Seleziona gli elenchi da stampare:</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="maggMin" id="defaultCheck1" name="elenchi[]"
                           checked>
                    <label class="form-check-label" for="defaultCheck1">
                        Popolazione Maggiorenni, Minorenni
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="effePostOspFig" id="defaultCheck1"
                           name="elenchi[]" checked>
                    <label class="form-check-label" for="defaultCheck1">
                        Effettivi, Postulanti, Ospiti, Figli
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="famiglie" id="defaultCheck2" name="elenchi[]"
                           checked>
                    <label class="form-check-label" for="defaultCheck2">
                        Famiglie
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="gruppi" id="defaultCheck2" name="elenchi[]"
                           checked>
                    <label class="form-check-label" for="defaultCheck2">
                        Gruppi familiari
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="aziende" id="defaultCheck2" name="elenchi[]"
                           checked>
                    <label class="form-check-label" for="defaultCheck2">
                        Aziende
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="incarichi" id="defaultCheck2"
                           name="elenchi[]" checked>
                    <label class="form-check-label" for="defaultCheck2">
                        Incarichi
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="scuola" id="defaultCheck2" name="elenchi[]"
                           checked>
                    <label class="form-check-label" for="defaultCheck2">
                        Scuola
                    </label>
                </div>
            </form>
        </template>
        <template slot="modal-button">
            <button class="btn btn-success" form="formStampa">Salva</button>
        </template>
    </my-modal>

    </div>
@endsection