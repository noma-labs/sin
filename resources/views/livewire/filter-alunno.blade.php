<div>
    <div class="form-group">
        <label for="annoScolastico">Anno Scolastico</label>
        <select class="form-control" id="annoScolastico" wire:change="selectAnnoScolastico($event.target.value)">
            @foreach ($anni as $anno)
                <option value="{{ $anno->id }}">{{ $anno->scolastico }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="inlineCheckbox1"  wire:model.live="selectedCicloOption" value="prescuola">
        <label class="form-check-label" for="inlineCheckbox1">Prescuola</label>
      </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="inlineCheckbox1"  wire:model.live="selectedCicloOption" value="elementari">
        <label class="form-check-label" for="inlineCheckbox1">Elementari</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="inlineCheckbox2" wire:model.live="selectedCicloOption" value="medie">
        <label class="form-check-label" for="inlineCheckbox2">Medie</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="inlineCheckbox3" wire:model.live="selectedCicloOption" value="superiori">
        <label class="form-check-label" for="inlineCheckbox3">Superiori</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="inlineCheckbox3" wire:model.live="selectedCicloOption" value="all">
        <label class="form-check-label" for="inlineCheckbox3">Tutti i cicli</label>
      </div>

      @foreach ($classiOptions as $classe)
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="inlineCheckbox1"  wire:model="selectedClassiOptions" value="{{ $classe->id }}">
            <label class="form-check-label" for="inlineCheckbox1">{{ $classe->nome }}</label>
        </div>
      @endforeach

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox" id="selectAllCheckbox" wire:click="toggleSelectAll">
        <label class="form-check-label" for="selectAllCheckbox">Seleziona/Deseleziona Tutti</label>
    </div>

    <div class="form-group">
        <label for="studenti">Studenti ({{count($students)}})</label>
            <ul>
                @foreach ($students as $student)
                <li> {{ $student->nominativo }}  {{ $student->ciclo }} </li>
                @endforeach
            </ul>
        </select>
    </div>
</div>
