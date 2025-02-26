@extends('layout.main')

@section('title', 'Manutenzione')

@section('content')
@include('partials.header', ['title' => 'Ricerca Manutenzione'])
    <ricerca-manutenzione 
        api-mezzi="{{route('api.mezzi')}}"
        api-manutenzioni="{{route('api.manutenzioni')}}"
        @isset($mezzo)
            :mezzo={{$mezzo->id}}
        @endisset>
    </ricerca-manutenzione>
@endsection