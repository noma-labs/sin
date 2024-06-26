@extends("layouts.app")

@section("title", "biblioteca")

@section("navbar-link")
    <li class="nav-item">
        <a class="nav-link" href="{{ route("biblioteca") }}">Biblioteca</a>
    </li>
@endsection

@section("content")
    <div class="row">
        <div class="col-md-4 offset-md-2">
            <div class="card" style="width: 17rem">
                <div class="card-body">
                    <h3 class="card-title">Libri</h3>
                    <p class="card-text">
                        Gestione dei libri nella biblioteca di Nomadelfia.
                    </p>
                    <a
                        href="{{ route("libri.ricerca") }}"
                        class="btn btn-primary"
                    >
                        Gestione Libri
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="width: 17rem">
                <div class="card-body">
                    <h3 class="card-title">DVD</h3>
                    <p class="card-text">
                        Gestione dei DVD presenti nella biblioteca di
                        Nomadelfia.
                    </p>
                    <a href="{{ route("video") }}" class="btn btn-primary">
                        Gestione DVD
                    </a>
                </div>
            </div>
            <!-- <a href="{{ route("video") }}">
      <div class="jumbotron ">
        <h2 class="text-center">Gestione DVD Biblioteca</h2>
      </div>
    </a> -->
        </div>
    </div>
@endsection
