@extends('officina.index')

@section('archivio')

@include('partials.header', ['title' => 'Nuovo Veicolo'])

<form method="post" action="{{ route('veicoli.create') }}">
    {{ csrf_field() }}
    <veicolo-create-form>
      <template slot="alimentazioni">
        @foreach ($alimentazioni as $alimentazione)
          <option value="{{ $alimentazione->id }}" @if($alimentazione->id == old('alimentazione')) selected @endif>{{ $alimentazione->nome }}</option>
        @endforeach
      </template>
      <template slot='marche'>
        @foreach ($marche as $marca)
          <option value="{{ $marca->id }}" @if($marca->id == old('marca')) selected @endif>{{ $marca->nome }}</option>
        @endforeach
      </template>
      <template slot='impieghi'>
        @foreach ($impieghi as $impiego)
          <option value="{{ $impiego->id }}" @if($impiego->id == old('impiego')) selected @endif>{{ $impiego->nome }}</option>
        @endforeach
      </template>
      <template slot='tipologie'>
        @foreach ($tipologie as $tipologia)
          <option value="{{ $tipologia->id }}" @if($tipologia->id == old('tipologia')) selected @endif>{{ $tipologia->nome }}</option>
        @endforeach
      </template>
    </veicolo-create-form>
</form>

@endsection
