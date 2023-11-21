@extends('nomadelfia.persone.index')

@section('archivio')

@include('partials.header', ['title' => 'Nuovo Matrimonio'])

<div class="row">
    <div class="col-md-6 offset-md-3">
        <h4>Dati Famiglia</h4>
        <form method="POST" action="{{route('nomadelfia.famiglie.create.confirm')}}">
            {{ csrf_field() }}
            <div class="form-group row">
                <label for="fornome" class="col-md-6 col-form-label">Marito</label>
                <div class="col-md-6">
                  <autocomplete placeholder="Inserisci marito..." name="persona_id" url={{route('api.nomadeflia.popolazione.search')}}></autocomplete>
                </div>
            </div>
             <div class="form-group row">
                            <label for="fornome" class="col-md-6 col-form-label">Moglie</label>
                            <div class="col-md-6">
                              <autocomplete placeholder="Inserisci moglie..." name="persona_id" url={{route('api.nomadeflia.popolazione.search')}}></autocomplete>
                            </div>
                        </div>
            <div class="form-group row">
                <label for="fordatainizio" class="col-md-6 col-form-label">Data matrimonio</label>
                <div class="col-md-6">
                    <date-picker :bootstrap-styling="true" value="{{ old('data_inizio') }}" format="yyyy-MM-dd"
                                 name="data_matrimonio"></date-picker>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <button class="btn btn-success" name="_addonly" value="true" type="submit">Salva</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
