@extends("nomadelfia.persone.index")

@section("content")
  @include("partials.header", ["title" => "Nuovo Matrimonio"])
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <form method="POST" action="{{ route("nomadelfia.marriage.store") }}">
        @csrf
        <div class="row mb-3">
          <label class="col-md-6 form-label"> Marito </label>
          <div class="col-md-6">
            <livewire:search-persona
              name_input="husband"
              placeholder="---Cerca il marito---"
              :persone_id="old('husband') ? [(int) old('husband')] : $husbandId"
            />
          </div>
        </div>
        <div class="row mb-3">
          <label class="col-md-6 form-label"> Moglie </label>
          <div class="col-md-6">
            <livewire:search-persona
              name_input="wife"
              placeholder="---Cerca la moglie---"
              :persone_id="old('wife') ? [(int) old('wife')] : $wifeId"
            />
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
            <button class="btn btn-success" type="submit">Aggiungi</button>
          </div>
        </div>
      </form>
    </div>
  </div>
@endsection
