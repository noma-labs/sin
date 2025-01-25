@extends("scuola.index")

@section("title", "Storico Anni scolastici")

@section("content")
    @include("scuola.templates.aggiungiAnnoScolastico")
    @foreach (collect($anni)->chunk(3) as $chunk)
        <div class="row">
            @foreach ($chunk as $anno)
                <div class="col-md-4 my-1">
                    <div id="accordion">
                        <div class="card">
                            <div
                                class="card-header"
                                id="heading{{ $anno->id }}"
                            >
                                <h5 class="mb-0">
                                    <a
                                        class="btn btn-link"
                                        href="{{ route("scuola.anno.show", $anno->id) }}"
                                    >
                                        {{ $anno->scolastico }}
                                    </a>
                                    <a
                                        class="btn btn-link bttn"
                                        href="{{ route("scuola.anno.show.new", $anno->id) }}"
                                    >
                                        (nuova versione)
                                    </a>
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $anno->alunni_count }}
                                    </span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
