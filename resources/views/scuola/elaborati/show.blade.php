@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Elaborato"])

    <div class="row">
        <!-- Left Column for Elaborato Information -->
        <div class="col-md-6">
            <h2 class="mb-3">Titolo: {{ $elaborato->titolo }}</h2>
            <p class="mb-2"><strong>Anno Scolastico:</strong> {{ $elaborato->anno_scolastico }}</p>
            <p class="mb-2"><strong>Classi:</strong> {{ $elaborato->classi }}</p>
            <p class="mb-3"><strong>Sommario:</strong> {{ $elaborato->sommario }}</p>
            <a href="{{ route('scuola.elaborati.download', $elaborato->id) }}" class="btn btn-primary">Scarica</a>
        </div>

        <!-- Right Column for PDF Preview -->
        <div class="col-md-6 ">
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="{{ $preview }}" allowfullscreen>
                    This browser does not support PDFs. Please download the PDF to view it: <a href="{{ route('scuola.elaborati.download', $elaborato->id) }}">Download PDF</a>.
                </iframe>
            </div>
        </div>
    </div>



@endsection
