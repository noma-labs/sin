@extends('biblioteca.libri.index')
@section('archivio')

<div class="row">
  <div class="pull-right">
    <h4><strong>{{ViewClientiBiblioteca::count()}} </strong> Clienti </h4>
</div>

@include('partials.header', ['title' => 'Ricerca Cliente'])



  <!-- <h1>Ricerca Clienti</h1> -->
     <!-- <form method="GET" class="form-inline" action="/biblioteca/libri/ricerca">
       {{ csrf_field() }}
       <div class="form-group">
         <label for="xCollocazione" class=" control-label"  >Collocazione </label>
         <input class="form-control" name="xCollocazione" type="text" id="xCollocazione" placeholder="Ins. Collocazione" >
      </div>

       <div class="form-group">
         <label for="xTitolo" class=" control-label" >Titolo </label>
        <input class="form-control" name="xTitolo" type="text" id="xTitolo"  placeholder="Inserisci Titolo Libro" >
      </div>

     <div class="form-group">
       <label for="xAutore" class="control-label" >Autore</label>
        <input class="form-control" name="xAutore" type="text" id="xAutore" size="10" maxlength="10" placeholder="Ins. Autore" />
      </div>

     <div class="form-group">
      <label for="xEditore" class="control-label">Editore</label>
      <input class="form-control" name="xEditore" type="text" id="xEditore" size="10" maxlength="10" placeholder="Ins. Editore" />
    </div>

     <div class="form-group">
       <label for="xNote" class="control-label"  >Note </label>
       <input class="form-control" name="xNote" type="text" id="xNote" size="20" maxlength="100" placeholder="Ins. Parola da ricercare nelle note" />
     </div>

    <div class="form-group">
      <button class="btn btn-success"   name="biblioteca"  type="submit">Cerca</button>

    </div>
    </form>
 -->

  <div class="alert alert-info alert-dismissable fade in">Ricerca effettuata:<strong> {{$msgSearch}}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  </div>

  <!-- <div class="alert alert-info alert-dismissable fade in"><strong> {{$query}}</strong>
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  </div> -->


<div class="table-responsive">
  <table class="table table-condensed">
    <table class='table table-bordered'>
      <thead class='thead-inverse'>
        <tr>
          <th>NOMINATIVO.</th>
          <!-- <th>NOME</th>
          <th>COGNOME</th> -->
        </tr>
   </thead>
  <tbody>
  @forelse ($clienti as $cliente)
      <tr>
        <td onclick="gotoClienteDetails({{ $cliente->id }} )" width="10">{{ $cliente->nominativo }}</td>
      </tr>
  @empty
      <div class="alert alert-danger">
          <strong>Nessun risultato ottenuto</strong>
      </div>
  @endforelse
</tbody>
</table>
</div>

<button class="btn btn-success"  name="inizio"  onClick=toTop()>INIZIO</button>
<!-- <button class="btn btn-success"  name="inizio"  onClick=clearSearch()>Nuova Ricerca</button> -->




@endsection
