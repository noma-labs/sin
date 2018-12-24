<h2>Patenti</h2>

<div class="container">
 <div class="row">
  <div class="col-md-4">
    <div id="accordion">
        <div class="card">
        <div class="card-header" id="headingOne">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
              Categoria A ({{App\Patente\Models\CategoriaPatente::dalNome("A")->patenti->count()}})
            </button>
          </h5>
        </div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
          <div class="card-body">
            <ul>
            @foreach(App\Patente\Models\CategoriaPatente::dalNome("A")->patenti()->get() as $patente)
                 <li>{{$patente->persona->nominativo}}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div> <!-- end nomadelfi effettivi card -->
    </div> <!-- end accordion -->
  </div> <!-- end first col-md-4 -->
 
  <div class="col-md-4">
   <div id="accordion">
    <div class="card">
      <div class="card-header" id="headingOne">
        <h5 class="mb-0">
          <button class="btn btn-link" data-toggle="collapse" data-target="#collapsePostulanti" aria-expanded="true" aria-controls="collapseOne">
          Categoria B ({{App\Patente\Models\CategoriaPatente::dalNome("B")->patenti->count()}})
          </button>
        </h5>
      </div>
      <div id="collapsePostulanti" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
        <div class="card-body">
            <ul>
            @foreach(App\Patente\Models\CategoriaPatente::dalNome("B")->patenti()->get() as $patente)
                 <li>{{$patente->persona->nominativo}}</li>
              @endforeach
            </ul>
        </div>
      </div><!-- end card postulanto -->
    </div> 
   </div> <!-- end accordion second colum-->
  </div> <!-- end second col-md-4 -->
 
  <div class="col-md-4">
   <div id="accordion">
      <div class="card">
        <div class="card-header" id="headingOne">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapseFigli" aria-expanded="true" aria-controls="collapseOne">
             Categoria C ({{App\Patente\Models\CategoriaPatente::dalNome("C")->patenti->count()}})
            </button>
          </h5>
        </div>
        <div id="collapseFigli" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
          <div class="card-body">
          <ul>
              @foreach(App\Patente\Models\CategoriaPatente::dalNome("C")->patenti()->get() as $patente)
                  <li>{{$patente->persona->nominativo}}</li>
                @endforeach
              </ul>
          </div>
        </div>
      </div> <!-- end card figli -->
    </div>  <!-- end accordion third row -->
   </div>  <!-- end third  col-md-4 -->
  <div> <!-- end row -->
</div> <!-- end container -->