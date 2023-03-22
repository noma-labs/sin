@extends('layouts.app')

@section('navbar-link')
	<li class='nav-item'><a href="{{ route('rtn.index') }}" class="nav-link"><i class="fa fa-home"></i> Home</a> </li>
	<li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-video-camera"></i> Archivio Film
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <a class="dropdown-item" href="{{ route('film.search') }}">Ricerca</a>
          <a class="dropdown-item" href="#">Inserimento</a>
          <a class="dropdown-item" href="#">Ultima Trasmissione</a>
          <a class="dropdown-item" href="#">Modifica</a>
          <a class="dropdown-item" href="#">Prestiti</a>
        </div>
    </li>
@endsection 
