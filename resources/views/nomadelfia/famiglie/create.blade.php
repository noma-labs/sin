@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Nuova Famiglia"])

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <form
                method="POST"
                action="{{ route("nomadelfia.matrimonio.store") }}"
            >
                @csrf
                <div class="row mb-3">
                    <label for="Husband" class="col-md-6 form-label">
                        Padre
                    </label>
                    <div class="col-md-6">
                        <livewire:search-persona name_input="husband" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="forFemale" class="col-md-6 form-label">
                        Madre
                    </label>
                    <div class="col-md-6">
                        <livewire:search-persona name_input="wife" />
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="datamatrimonio" class="col-md-6 form-label">
                        Data matrimonio
                    </label>
                    <div class="col-md-6">
                        <input
                            class="form-control"
                            type="date"
                            name="data_matrimonio"
                            value="{{ old("data_matrimonio") }}"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-auto">
                        <button class="btn btn-success" type="submit">
                            Aggiungi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
