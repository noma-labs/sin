@extends('patente.index')

@section('archivio')
   
<sin-header title="Stampa elenchi Patente"> </sin-header>

<div class="row">
    <div class="col-md-3">
        <form action="{{route('patente.elenchi.stampa.autorizzati')}}">
            <button type="submit" class="btn btn-block btn-primary">Stampa Conducenti autorizzati</button>
        </form>
    </div>
</div>
@endsection