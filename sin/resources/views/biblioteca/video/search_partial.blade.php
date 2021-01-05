<div class="my-page-title">
    <div class="d-flex justify-content-end">
    <div class="mr-auto p-2"><span class="h1 text-center">Ricerca DVD </span></div>
    <div class="p-2 text-right">
      <h5 class="m-1">{{App\Biblioteca\Models\Video::count()}} DVD presenti nella Biblioteca.</h5 >
    </div>
  </div>
</div>


<form method="GET" class="form" action="{{route('video.ricerca.submit') }}">
  {{ csrf_field() }}
  <div class="row">
   <div class="col-md-4">
     <label for="xTitolo" class=" control-label" >Collocazione DVD </label>
     <input class="form-control"  type="text"  name="cassetta" placeholder="Inserisci collocazione DVD (ex. ZA)" >
   </div>

  <div class="col-md-4">
    <div class="form-group">
     <label for="xClassificazione"  class="control-label" >Data registrazione </label>
     <input class="form-control"  type="text" name="data_registrazione" placeholder="Inserisci data registrazione" >
    </div>
  </div>
  <div class="col-md-4    ">
    <div class="form-group">
      <label for="xNote" class="control-label">Descrizione </label>
      <input class="form-control" name="descrizione" type="text" placeholder="Inserisci parola da ricercare nelle descrizione" />
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="btn-toolbar pull-right">
      <div><button class="btn btn-success"  id="biblio" name="biblioteca"  type="submit">Cerca DVD</button></div>
    </div>
  </div>
</div>

</form>
