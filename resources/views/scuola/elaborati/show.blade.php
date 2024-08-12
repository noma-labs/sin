@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Gestione elaborato"])

    <div class="row">
        <div class="col-md-5">
            <p class="mb-2">
                <strong>Titolo</strong>
                {{ $elaborato->titolo }}
            </p>
            <p class="mb-2">
                <strong>Anno Scolastico:</strong>
                {{ $elaborato->anno_scolastico }}
            </p>
            <p class="mb-2">
                <strong>Collocazione:</strong>
                {{ $elaborato->collocazione }}
            </p>
            <p class="mb-2">
                <strong>Classi:</strong>
                {{ $elaborato->classi }}
            </p>
            <p class="mb-3">
                <strong>Studenti:</strong>
                @forelse ($elaborato->studenti as $studente)
                    <span>
                        {{ $studente->nominativo }}
                    </span>
                @empty
                    Nessuno studente.
                @endforelse
            </p>
            <p class="mb-3">
                <strong>Note:</strong>
                {{ $elaborato->note }}
            </p>
            <a
                href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                class="btn btn-primary"
            >
                Scarica
            </a>
        </div>

        <div class="col-md-7 overflow-auto" style="height: 600px">
            <iframe
                src="{{ route("scuola.elaborati.preview", $elaborato->id) }}"
                style="width: 100%; height: 100%"
                allowfullscreen
            >
                This browser does not support PDFs. Please download the PDF to
                view it:
                <a
                    href="{{ route("scuola.elaborati.download", $elaborato->id) }}"
                >
                    Download PDF
                </a>
                .
            </iframe>
        </div>
    </div>
@endsection
