<div>
  <div class="row">
    <label for="fornascita" class="col-sm-6">Nome Cognome</label>
    <div class="col-sm-6">
      <p class="fw-bold">{{ $persona->nome }} {{ $persona->cognome }}</p>
    </div>
  </div>

  <div class="row">
    <label for="fornascita" class="col-sm-6">Data nascita</label>
    <div class="col-sm-6">
      <p class="fw-bold">{{ $persona->data_nascita }}</p>
    </div>
  </div>

  <div class="row">
    <label for="fornascita" class="col-sm-6">
      Come è entrato in Nomadelfia?
    </label>
    <div class="col-sm-6">
      <div class="form-check">
        <input
          class="form-check-input"
          type="radio"
          name="tipologia"
          wire:model.live="tipologia"
          id="tipologia-dalla-nascita"
          value="dalla_nascita"
        />
        <label class="form-check-label" for="tipologia-dalla-nascita">
          Nato a Nomadelfia
        </label>
      </div>

      <div class="form-check">
        <input
          class="form-check-input"
          type="radio"
          name="tipologia"
          wire:model.live="tipologia"
          id="tipologia-minorenne-accolto"
          value="minorenne_accolto"
        />
        <label class="form-check-label" for="tipologia-minorenne-accolto">
          Minorenne accolto
        </label>
      </div>

      <div class="form-check">
        <input
          class="form-check-input"
          type="radio"
          name="tipologia"
          wire:model.live="tipologia"
          id="tipologia-minorenne-famiglia"
          value="minorenne_famiglia"
        />
        <label class="form-check-label" for="tipologia-minorenne-famiglia">
          Minorenne entrato/a con la famiglia
        </label>
      </div>

      <div class="form-check">
        <input
          class="form-check-input"
          type="radio"
          name="tipologia"
          wire:model.live="tipologia"
          id="tipologia-maggiorenne-single"
          value="maggiorenne_single"
        />
        <label class="form-check-label" for="tipologia-maggiorenne-single">
          Maggiorenne single
        </label>
      </div>

      <div class="form-check">
        <input
          class="form-check-input"
          type="radio"
          name="tipologia"
          wire:model.live="tipologia"
          id="tipologia-maggiorenne-famiglia"
          value="maggiorenne_famiglia"
        />
        <label class="form-check-label" for="tipologia-maggiorenne-famiglia">
          Maggiorenne con famiglia
        </label>
      </div>
    </div>
  </div>

  <div class="row my-3">
    <label for="forentrata" class="col-sm-6">Data Entrata</label>
    <div class="col-sm-6">
      <input
        class="form-control"
        type="date"
        name="data_entrata"
        wire:model="dataEntrata"
      />
    </div>
  </div>

  @if ($showFamigliaSelect)
    <div class="row my-2">
      <label for="fornascita" class="col-sm-6">Famiglia</label>
      <div class="col-sm-6">
        <livewire:search-famiglia name_input="famiglia_id" />
      </div>
    </div>
  @endif

  @if ($showGruppoFamiliareSelect)
    <div class="row my-2">
      <label for="fornascita" class="col-sm-6">Gruppo Familiare:</label>
      <div class="col-sm-6">
        <select class="form-select" id="gruppo" name="gruppo_id">
          <option value="">--- Seleziona---</option>
          @foreach (App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare::orderby("nome")->get() as $gruppo)
            <option value="{{ $gruppo->id }}">{{ $gruppo->nome }}</option>
          @endforeach
        </select>
      </div>
    </div>
  @endif
</div>
