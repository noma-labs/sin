@extends("nomadelfia.index")

@section("content")
  @include("partials.header", ["title" => "Nuovo Matrimonio"])
  <div class="row">
    <div class="col-md-6 offset-md-3">
      <form method="POST" action="{{ route("anagrafe.marriage.store") }}">
        @csrf
        <div class="row mb-3">
          <label for="Husband" class="col-md-6 form-label"> Marito </label>
          <div class="col-md-6">
            <livewire:search-persona
              name_input="husband"
              :persone_id="$husbandId"
            />
          </div>
        </div>
        <div class="row mb-3">
          <label for="forFemale" class="col-md-6 form-label"> Moglie </label>
          <div class="col-md-6">
            <livewire:search-persona name_input="wife" :persone_id="$wifeId" />
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
          <label for="gruppo" class="col-md-6 form-label">
            Gruppo familiare
            <small class="d-block text-muted">
              Obbligatorio se uno degli sposi è in Nomadelfia
            </small>
          </label>
          <div class="col-md-6">
            <select class="form-select" id="gruppo" name="gruppo_id">
              <option value="">--- Seleziona ---</option>
              @foreach (App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare::orderby("nome")->get() as $gruppo)
                <option
                  value="{{ $gruppo->id }}"
                  @selected(old("gruppo_id") == $gruppo->id)
                >
                  {{ $gruppo->nome }}
                </option>
              @endforeach
            </select>
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
