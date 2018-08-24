@extends('biblioteca.libri.index')

@section('archivio')
@include('partials.header', ['title' => 'Aggiungi Libro'])

<form method="POST" action="{{route('libri.inserisci.Confirm')}}">
  {{ csrf_field() }}
  <div class="row">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-6">
          <search-collocazione title="Collocazione ({{App\Biblioteca\Models\ViewCollocazione::total()}}) - Lettere (*)"
                               url-lettere="{{route('api.biblioteca.collocazione')}}"
                               numeri-required="true"
                               numeri-assegnati="false">
          </search-collocazione>
        </div>
        <div class="col-md-6">
           <label for="xTitolo" class="control-label">Titolo (*) </label>
           <input class="form-control"
           name="xTitolo"
           type="text"
           value="{{ old('xTitolo')}}"
           placeholder="Inserisci titolo libro..." >
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
           <div class="row">
             <div class="col-md-8">
                 <label for="xAutore" class="control-label">Autore/i (*)</label>
                 <autocomplete :multiple="true"
                                placeholder="Inserisci autore/i ..."
                                name="xIdAutori"
                                url={{route('api.biblioteca.autori')}}
                                >
                  </autocomplete>
              </div>
             <div class="col-md-4">
                 <label>&nbsp;</label>
                 <modal title="Nuovo Autore" url-post="{{route('api.biblioteca.autori.create')}}" placeholder="Es. Italo Calvino">
             </div>
           </div>
        </div>
        <div class="col-md-6">
          <div class="row">
            <div class="col-md-8">
                <label for="xEditore" class="control-label">Editore/i (*)</label>
                <autocomplete :multiple="true"
                              placeholder="Inserisci editore/i ..."
                              name="xIdEditori"
                              url={{route('api.biblioteca.editori')}}>
                </autocomplete>
            </div>

            <div class="col-md-4">
              <label>&nbsp;</label>
              <modal title="Nuovo Editore" url-post="{{route('api.biblioteca.editori.create')}}" placeholder="Es. Arnoldo Mondadori"/>
            </div>
          </div>
         </div>
      </div>
      <div class="row">
          <div class="col-md-4">
            <label for="xClassificazione"  class="control-label" >Classificazione (*)</label>
             <select class="form-control"   name="xClassificazione" type="text" id="xClassificazione">
              <option disabled selected>---Seleziona la Classificazione---</option>
               @foreach ($classificazioni as $cls)
                 @if(old('xClassificazione') != null)
                   @if(old('xClassificazione') ==  $cls->id)
                   <option value={{ $cls->id}} selected> {{ $cls->descrizione}} </option>
                   @else
                   <option value={{ $cls->id}}> {{ $cls->descrizione}}</option>
                   @endif
                 @else
                  <option value={{ $cls->id}}> {{ $cls->descrizione}}</option>
                  @endif
              @endforeach
             </select>
          </div>
          <div class="col-md-4">
              <label for="dimensione">Dimensione</label>
              <input class="form-control" type="text" name="dimensione" value="{{ old('dimensione')}}" >
          </div>
          <div class="col-md-4">
              <label for="critica">Critica</label>
              <select class="form-control"   name="critica" type="text">
               <option disabled selected>---Seleziona la critica---</option>
               @foreach (App\Biblioteca\Models\Libro::getEnum('critica') as $crit)
                 <!-- <option value={{ $crit}}> {{$crit}}</option> -->
                 @if(old('critica') ==  $crit)
                 <option value={{$crit}} selected> {{$crit}}</option>
                 @else
                  <option value={{ $crit}}> {{$crit}}</option>
                 @endif
               @endforeach
              </select>
          </div>
      </div>
      <div class="row">
        <div class="col-md-4">
            <label for="isbn">ISBN</label>
            <input class="form-control" type="text"   value="{{ old('isbn')}}" name="isbn">
        </div>
        <div class="col-md-4">
            <label for="data_pubblicazione">Data pubblicazione</label>
            <input type="text" class="form-control" id="dataPubblicazione" value="{{ old('data_pubblicazione')}}" name="data_pubblicazione">
        </div>
        <div class="col-md-4">
            <label for="categoria">Categoria</label>
            <select class="form-control"   name="categoria" type="text">
               <option disabled selected>---Seleziona la categoria---</option>
              @foreach (App\Biblioteca\Models\Libro::getEnum('categoria') as $cat)
                @if(old('categoria') ==  $cat)
                <option value={{$cat}} selected> {{$cat}}</option>
                @else
                 <option value={{ $cat}}> {{$cat}}</option>
                @endif
             @endforeach
            </select>
        </div>
      </div>
      <div class="row">
          <div class="col-md-12">
            <label for="xNote" class="control-label">Note </label>
             <textarea class="form-control" name="xNote" class="text" rows="2">{{ old('xNote')}}</textarea>
          </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="form-check">
           <label class="form-check-label">
             <input class="form-check-input" type="checkbox"  name="xTobePrinted"   value="true"   checked>
             Aggiungi il nuovo libro inserito nella lista delle etichette da stampare.
           </label>
         </div>
       </div>
       <div class="col-md-4">
         <p class="text-right text-danger ">Le informazioni segnate con (*) sono obbligatorie.</p>
       </div>
      </div>
      <div class="row">
   <div class="col-md-12">
     <!-- <div class="form-group"> -->
       <button class="btn btn-success"   name="_addanother" value="true" type="submit">Salva e aggiungi un'altro Libro</button>
       <button class="btn btn-success"   name="_addonly" value="true" type="submit">Salva</button>
     <!-- </div> -->
  </div>
 </div>
    </div>
  </div>
</form>





@endsection
