@extends('layouts.app')

@section('archivio')

@section('navbar-link')
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true"
       aria-expanded="false">
        Gestione Autenticazione
    </a>

    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="{{url('admin/users')}}">Gestione utenti</a>
        <a class="dropdown-item" href="{{url('admin/roles')}}">Gestione ruoli</a>
        <a class="dropdown-item" href="{{url('admin/risorse')}}">Gestione permessi</a>
    </div>
</li>

<li class="nav-item"><a class="nav-link" href="{{ route('admin.backup') }}">Gestione Backup</a></li>
<li class="nav-item"><a class="nav-link" href="{{ route('admin.logs') }}">Gestione attività</a></li>
@endsection

@endsection
