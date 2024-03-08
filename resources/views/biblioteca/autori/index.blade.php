@extends("biblioteca.libri.index")
@section("title", "Autori")

@section("archivio")
    <div class="my-page-title">
        <div class="d-flex justify-content-end">
            <div class="mr-auto p-2">
                <span class="h1 text-center">Ricerca Autori</span>
            </div>
            <div class="p-2 text-right">
                <h5 class="m-1">
                    {{ App\Biblioteca\Models\Autore::count() }} Autori
                </h5>
            </div>
        </div>
    </div>

    <!-- <div class="row">
  <div class="col-md-4 offset-md-8">
    <a href="{{ route("autori.create") }}" class="btn btn-success">Aggiungi Autore</a>
  </div>
</div> -->

    <div class="row">
        <div class="col-md-8 offset-md-2 my-3">
            <span>Ricerca autore:</span>
            <form action="{{ route("autori.ricerca") }}" method="get">
                {{ csrf_field() }}
                <autocomplete
                    placeholder="Inserisci autore ..."
                    name="idAutore"
                    url="{{ route("api.biblioteca.autori") }}"
                ></autocomplete>
                <button class="btn btn-success my-2 float-right" type="submit">
                    Cerca
                </button>
            </form>
        </div>
    </div>

    <!--
<div class="row">
  <div class="col-md-8 offset-md-2">
    <table class="table table-striped table-bordered table-hover">
       <thead class='thead-inverse'>
         <tr>
           <th>Autore</th>
           <th>Operazioni</th>
         </tr>
       </thead>
       <tbody>
     @forelse ($autori as $autore)
         <tr>
           <td>{{ $autore->autore }}</td>
           <td>
             <div class="btn-toolbar" role="toolbar" aria-label="...">
                <div class="btn-group" role="group" aria-label="...">
                 <a href="{{ route("autori.edit", $autore->id) }}" class="btn btn-info" >Modifica</a></div>
                <div class="btn-group" role="group" >
                  {!! Form::open(["method" => "DELETE", "route" => ["autori.destroy", $autore->id]]) !!}
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
   {{ $autori->links() }}
 </div>
</div> -->
@endsection
