@extends('patente.index')

@section('archivio')
   
<sin-header title="Elenchi Patente"> </sin-header>

@include('patente.elenchi.percategoria')

<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="btn-group my-3">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Esporta conducenti autorizzati
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{route('patente.elenchi.autorizzati.esporta.pdf')}}">.pdf</a>

                    <a class="dropdown-item" href="{{route('patente.elenchi.autorizzati.esporta.excel')}}">.excel</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="btn-group my-3">
                <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Esporta patenti
                </button>
                <div class="dropdown-menu">
                    {{-- <a class="dropdown-item" href="{{route('patente.elenchi.patenti.esporta.pdf')}}">.pdf</a> --}}
                    <a class="dropdown-item" href="{{route('patente.elenchi.patenti.esporta.excel')}}">.excel</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
