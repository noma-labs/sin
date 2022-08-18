<div class="container">
  @foreach (App\Patente\Models\CategoriaPatente::all()->chunk(4) as $chunk)
    <div class="row my-2" id="accordionExample">
        @foreach ($chunk as $categoria)
            <div class="col-md-3">
                  <div class="card">
                    <div class="card-header" id="heading{{ $categoria->id }}">
                      <h5 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#categoria{{ $categoria->id }}" aria-expanded="false" aria-controls="{{ $categoria->id }}">
                      Categoria {{ $categoria->categoria }} ({{ $categoria->patenti->count() }})
                        </button>
                      </h5>
                    </div>
                    <div id="categoria{{ $categoria->id }}" class="collapse" aria-labelledby="heading{{ $categoria->id }}" data-parent="#accordionExample">
                      <div class="card-body">
                        <ul>
                        @foreach($categoria->patenti()->get()->sortBy('nominativo') as $patente)
                            <li>{{$patente->persona->nominativo}}</li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                </div> <!-- end card -->
            </div>
        @endforeach
    </div>
  @endforeach

    @foreach (App\Patente\Models\CQC::all()->chunk(2) as $chunk)
    <div class="row">
        @foreach ($chunk as $cqc)
            <div class="col-md-6">
              <div id="accordion">
                  <div class="card">
                  <div class="card-header" id="headingOne">
                    <h5 class="mb-0">
                      <button class="btn btn-link" data-toggle="collapse" data-target="#cqc{{ $cqc->id }}" aria-expanded="true" aria-controls="{{ $cqc->id }}">
                      {{ $cqc->categoria }} ({{ $cqc->patenti->count() }})
                      </button>
                    </h5>
                  </div>
                  <div id="cqc{{ $cqc->id }}" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                      <ul>
                      @foreach($cqc->patenti()->get() as $patente)
                          <li>{{$patente->persona->nominativo}}</li>
                        @endforeach
                      </ul>
                    </div>
                  </div>
                </div> <!-- end nomadelfi effettivi card -->
              </div> <!-- end accordion -->
            </div>
        @endforeach
    </div>
  @endforeach
</div> <!-- end container -->