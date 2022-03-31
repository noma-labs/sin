@extends('layouts.app')

@section('title', 'Stazione Meteo')

@section('archivio')
<div class="row">
  {{config('stazione-meteo.grafana.url')}}
  <div class="col-md-4 offset-md-2">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title">Temperatura</h3>
          <iframe src="{{config('stazione-meteo.grafana.temp_panel_url')}}" width="450" height="200" frameborder="0"></iframe>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title">Anemometro</h3>
        <iframe src="{{config('stazione-meteo.grafana.anem_panel_url')}} }}" width="450" height="200" frameborder="0"></iframe>
      </div>
    </div>
  </div>
</div>
@endsection
