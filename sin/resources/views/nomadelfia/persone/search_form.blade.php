
<form method="GET" action="{{ route('nomadelfia.persone.ricerca.submit') }}">
    {{ csrf_field() }}
    <div class="row">
       <div class="col-md-2">
           <label class="control-label">Nominativo </label>
           <input class="form-control" name="nominativo" type="text" placeholder="Inserisci il nominativo..." />
        </div>
       <div class="col-md-2">
         <label class="control-label">Nome </label>
         <input class="form-control" name="nome" type="text" placeholder="Inserisci il nome." />
       </div>
 
     <div class="col-md-2">
       <div class="form-group">
         <label class="control-label">Cognome </label>
         <input class="form-control" name="cognome" type="text" placeholder="Inserisci il cognome" />
       </div>
     </div>
 
     <div class="col-md-2">
         <div class="form-group">
           <label class="control-label">Data di Nascita</label>
           <select class="form-control"  name="criterio_data_nascita" type="text" >
               <option value="<">Minore</option>
               <option value="<=" selected>Minore Uguale</option>
               <option value="=">Uguale</option>
               <option value=">">Maggiore</option>
               <option value=">=">Maggiore Uguale</option>
           </select>
         </div>
       </div>
       <div class="col-md-2">
         <label >&nbsp;</label>
         <input type="date" class="form-control" id="data_nascita" name="data_nascita" >
       </div>

   </div>
    <div class="row align-items-end">
      
       {{-- <div class="col-md-3">
         <div class="form-group">
           <label for="uso">Categoria</label>
           <select class="form-control" id="uso" name="uso_id" required>
             <option disabled selected>--Seleziona--</option>
             @foreach (App\Nomadelfia\Models\Categoria::all() as $categoria)
             <option value="{{ $categoria->id }}">{{ $categoria->nome }} {{ $categoria->descrizione }}</option>
             @endforeach
         </select>
         </div>
       </div> --}}
       
       <div class="col-md-2 offset-md-6">
          <div class="form-group">
            <input type="checkbox" id="stato" name="stato">
            <label class="control-label">Includi persone disattivate</label>
          </div>
       </div>


       <div class="col-md-2">
           <div class="form-group">
             <label id="lab">&nbsp;</label>
             <button type="submit" class="btn btn-block btn-primary">Ricerca</button>
           </div>
         </div>
   </div>
 </form>