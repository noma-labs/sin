@extends("biblioteca.libri.index")
@section("title", "Editori")

@section("archivio")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="mr-auto p-2">
                <span class="h1 text-center">Gestione Editori</span>
            </div>
            <div class="p-2 text-right">
                <h5 class="m-1">
                    {{ App\Biblioteca\Models\Editore::count() }} Editori
                </h5>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <span>Ricerca editore:</span>
            <form action="{{ route("editori.ricerca") }}" method="get">
                {{ csrf_field() }}
                <autocomplete
                    placeholder="Inserisci editore ..."
                    name="idEditore"
                    url="{{ route("api.biblioteca.editori") }}"
                ></autocomplete>
                <button class="btn btn-success my-2 float-right" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>

    <!-- <div class="row">
  <div class="col-md-8 offset-md-2">
    <a href="{{ route("editori.create") }}" class="btn btn-success">Aggiungi Editore</a>
  </div>
</div> -->
    <!--
<div class="row">
  <div class="col-md-8 offset-md-2">
    <table class="table table-striped table-bordered table-hover">
       <thead class='thead-inverse'>
         <tr>
           <th>Editore</th>
           <th>Operazioni</th>
         </tr>
       </thead>
       <tbody>
     @forelse ($editori as $editore)
         <tr>
           <td>{{ $editore->editore }}</td>
           <td>
             <div class="btn-toolbar" role="toolbar" aria-label="...">
                <div class="btn-group" role="group" aria-label="...">
                 <a href="{{ route("editori.edit", $editore->id) }}" class="btn btn-info pull-left" >Modifica</a></div>
                <div class="btn-group" role="group" aria-label="...">
                  {!! Form::open(["method" => "DELETE", "route" => ["editori.destroy", $editore->id]]) !!}
                  {!! Form::submit("Elimina", ["class" => "btn btn-danger"]) !!}
                  {!! Form::close() !!}
                </div>
             </div>
           </td>
         </tr>
@empty









         <tr>
           <td>Nessun autore presente</td>
           <td></td>
         </tr>
         @endforelse
       </tbody>
     </table>
   {{ $editori->links() }}
 </div>
</div> -->
@endsection
