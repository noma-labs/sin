@extends("layouts.app")

@section("content")
    <div class="container">
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            @if (Auth::guest())
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Biblioteca</div>
                        <div class="card-body">
                            <p class="card-text">
                                Ricerca libri e video della biblioteca di
                                Nomadelfia.
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ url("/biblioteca") }} "
                                class="btn btn-primary"
                            >
                                dsaf
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col">
                <div class="card text-center h-100">
                    <div class="card-header">Stazione Meteo Nomadelfia</div>
                    <div class="card-body">
                        <p class="card-text">
                            Stazione meteo situata nella zona delle scuole
                        </p>
                    </div>
                    <div class="card-footer">
                        <a
                            target="_blank"
                            href="http://192.168.11.7:3000/d/z-qyiG1Mk/weather?orgId=1&from=now-6h&to=now"
                            class="btn btn-primary"
                        >
                            Accedi
                        </a>
                    </div>
                </div>
            </div>
            @hasrole("super-admin")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">RTN</div>
                        <div class="card-body">
                            <p class="card-text">Gestione video storico di RTN.</p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ route("rtn.video.index") }}"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endhasrole
            @can("biblioteca.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Biblioteca</div>
                        <div class="card-body">
                            <p class="card-text">
                                Gestione libri e video della biblioteca di
                                Nomadelfia.
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ url("/biblioteca") }} "
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            @can("agraria.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Agraria</div>
                        <div class="card-body">
                            <p class="card-text">
                                Gestione mezzi agricoli dell'azienda agraria
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="http://192.168.11.7:8080/"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            @can("meccanica.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Officina</div>
                        <div class="card-body">
                            <p class="card-text">Gestione mezzi di Nomadelfia</p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ route("officina.index") }}"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            @can("popolazione.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Gestione Nomadelfia</div>
                        <div class="card-body">
                            <p class="card-text">
                                Popolazione Nomadelfia, Aziende, Gruppi familiari,
                                Famiglie
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ route("nomadelfia.index") }}"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            @can("scuola.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Gestione Scuola</div>
                        <div class="card-body">
                            <p class="card-text">
                                Gestione classi, alunni cordinatori
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ route("scuola.summary") }}"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            @hasrole("super-admin")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Amministratore</div>
                        <div class="card-body">
                            <p class="card-text">
                                Pannello di controllo per la gestione degli utenti,
                                permessi, backup e logs di tutti i sitemi.
                            </p>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route("admin") }}" class="btn btn-primary">
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endhasrole
            @can("scuolaguida.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Patenti</div>
                        <div class="card-body">
                            <p class="card-text">
                                Pannello di controllo per la gestione delle patenti
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ route("patente.scadenze") }}"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            @can("archivio.visualizza")
                <div class="col">
                    <div class="card text-center h-100">
                        <div class="card-header">Archivio Libri</div>
                        <div class="card-body">
                            <p class="card-text">
                                Gestione archivio libri di Nomadelfia
                            </p>
                        </div>
                        <div class="card-footer">
                            <a
                                href="{{ route("archiviodocumenti") }}"
                                class="btn btn-primary"
                            >
                                Accedi
                            </a>
                        </div>
                    </div>
                </div>
            @endcan
            <div class="col">
                <div class="card text-center h-100">
                    <div class="card-header">Archivio Libri</div>
                    <div class="card-body">
                        <p class="card-text">
                            Gestione archivio libri di Nomadelfia
                        </p>
                    </div>
                    <div class="card-footer">
                        <a
                            href="{{ route("archiviodocumenti") }}"
                            class="btn btn-primary"
                        >
                            Accedi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
