@extends('patente.index')

@section('archivio')
   
<sin-header title="Elenchi Patente"> </sin-header>

<div class="row">
    <div class="col-md-3">
        <div class="btn-group">
            <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Conducenti autorizzati
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{route('patente.elenchi.stampa.autorizzati')}}">Esporta in .pdf</a>

                <a class="dropdown-item" href="#">Esporta in .excel</a>
            </div>
        </div>
    </div>
</div>
@endsection
