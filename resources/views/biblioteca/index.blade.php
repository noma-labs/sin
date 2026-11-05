@extends("layouts.app")

@section("title", "biblioteca")

@section("navbar-link")
  <li class="nav-item">
    <a class="nav-link" href="{{ route("biblioteca") }}">Biblioteca</a>
  </li>
@endsection

@section("content")
  <div class="row mt-3 g-3 justify-content-center">
    <div class="col-md-4 col-lg-3">
      <div class="card" style="width: 17rem">
        <div class="card-body">
          <h3 class="card-title">Libri</h3>
          <p class="card-text">Gestione dei libri nella biblioteca di Nomadelfia.</p>
          <a href="{{ route("books.index") }}" class="btn btn-primary">
            Gestione Libri
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-lg-3">
      <div class="card" style="width: 17rem">
        <div class="card-body">
          <h3 class="card-title">Mappa</h3>
          <p class="card-text">Dove si trovano i libri e gli argomenti di ogni scaffale.</p>
          <a href="{{ route("books.map") }}" class="btn btn-primary">
            Apri la mappa
          </a>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-lg-3">
      <div class="card" style="width: 17rem">
        <div class="card-body">
          <h3 class="card-title">DVD</h3>
          <p class="card-text">Gestione dei DVD presenti nella biblioteca di Nomadelfia.</p>
          <a href="{{ route("video") }}" class="btn btn-primary">
            Gestione DVD
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection
