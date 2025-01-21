@extends("biblioteca.index")

@section("navbar-link")
@parent
<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        href="#"
        id="navbarDropdownMenuLink"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        DVD
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="dropdown-item" href="#">Video eliminati</a>
    </div>
</li>
@append
