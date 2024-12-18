@extends("scuola.index")

@section("content")
    @include("partials.header", ["title" => "Aggiungi alunni a elaborato"])

    <form
        method="POST"
        action="{{ route("scuola.elaborati.students.store", $elaborato->id) }}"
    >
        @csrf
        <livewire:filter-alunno
            scolastico="{{ $elaborato->anno_scolastico }}"
        />

        <button>Aggiungi</button>
    </form>
@endsection
