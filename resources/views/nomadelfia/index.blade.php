@extends("layouts.app")

@section("title", "Gestione Nomadelfia")

@section("navbar-link")
<li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.index") }}">Nomadelfia</a>
</li>
<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        id="navbarPopolazione"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Popolazione
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPopolazione">
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.popolazione.maggiorenni") }}"
        >
            Maggiorenni
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.popolazione.posizione.effettivi") }}"
        >
            Effettivi
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.popolazione.posizione.postulanti") }}"
        >
            Postulanti
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.popolazione.posizione.ospiti") }}"
        >
            Ospiti
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.popolazione.posizione.figli.maggiorenni") }}"
        >
            Figli Mag.
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.popolazione.posizione.figli.minorenni") }}"
        >
            Figli Min.
        </a>
    </div>
</li>

<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        id="navbarPesone"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Persone
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarPesone">
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.persone.ricerca") }}"
        >
            Ricerca persone
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.persone.create") }}"
        >
            Inserisci Persona
        </a>
    </div>
</li>

<li class="nav-item dropdown">
    <a
        class="nav-link dropdown-toggle"
        id="navbarFamiglie"
        role="button"
        data-bs-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
    >
        Famiglie
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarFamiglie">
        <a class="dropdown-item" href="{{ route("nomadelfia.famiglie") }}">
            Gestione famiglie
        </a>
        <a
            class="dropdown-item"
            href="{{ route("nomadelfia.matrimonio.create") }}"
        >
            Nuovo Matrimonio
        </a>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.gruppifamiliari") }}">
        Gruppi familiari
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.aziende") }}">Aziende</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.incarichi.index") }}">
        Incarichi
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.esercizi") }}">
        Es. Spirituali
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route("nomadelfia.cariche.index") }}">
        Cariche
    </a>
</li>

@append
