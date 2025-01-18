@extends("nomadelfia.persone.index")

@section("content")
    @include("partials.header", ["title" => "Nuovo Matrimonio"])

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <form
                method="POST"
                action="{{ route("nomadelfia.matrimonio.store") }}"
            >
                {{ csrf_field() }}
                <div class="form-group row">
                    <label for="Husband" class="col-md-6 col-form-label">
                        Marito
                    </label>
                    <div class="col-md-6">
                        <select class="form-control" name="husband" type="text">
                            <option disabled selected>
                                ---Seleziona il marito---
                            </option>
                            @foreach ($singleMale as $m)
                                <option value="{{ $m->id }}">
                                    {{ $m->nominativo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="forFemale" class="col-md-6 col-form-label">
                        Moglie
                    </label>
                    <div class="col-md-6">
                        <select class="form-control" name="wife" type="text">
                            <option disabled selected>
                                ---Seleziona la moglie---
                            </option>
                            @foreach ($singleFemale as $f)
                                <option value="{{ $f->id }}">
                                    {{ $f->nominativo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="datamatrimonio" class="col-md-6 col-form-label">
                        Data matrimonio
                    </label>
                    <div class="col-md-6">
                        <input class="form-control" type="date" name="data_matrimonio"  value="{{ old("data_matrimonio") }}"/>
                    </div>
                </div>
                <div class="row">
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
