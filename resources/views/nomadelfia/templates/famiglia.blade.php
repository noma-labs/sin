<ul class="list-group list-group-flush">
    @forelse ($componenti as $componente)
        <li class="list-group-item">
            <div class="row">
                <label class="col-sm-2">
                    {{ $componente->posizione_famiglia }}
                </label>
                <div class="col-sm-8">
                    <span>
                        @year($componente->data_nascita)
                        @include("nomadelfia.templates.persona", ["persona" => $componente])
                    </span>
                    @if ($componente->stato == "1")
                        <span class="badge badge-pill bg-success">
                            Nel nucleo
                        </span>
                    @else
                        <span class="badge badge-pill bg-danger">
                            Fuori da nucleo
                        </span>
                    @endif
                </div>
                <div class="col-sm-2">
                    @include("nomadelfia.templates.aggiornaComponente", ["componente" => $componente])
                </div>
            </div>
        </li>
    @empty
        <p class="text-danger">Nessun componente</p>
    @endforelse

    @include("nomadelfia.templates.aggiungiComponente", ["famiglia" => $famiglia])
</ul>
