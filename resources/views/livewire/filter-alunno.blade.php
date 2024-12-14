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
        <input class="form-check-input" type="checkbox"  id="inlineRadio0" value="all" wire:model="selectedOption">
        <label class="form-check-label" for="inlineRadio0">tutti</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox"  id="inlineRadio1" value="elementari" wire:model="selectedOption">
        <label class="form-check-label" for="inlineRadio1">elementari</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox"  id="inlineRadio2" value="medie" wire:model="selectedOption">
        <label class="form-check-label" for="inlineRadio2">medie</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox"  id="inlineRadio3" value="superiori" wire:model="selectedOption">
        <label class="form-check-label" for="inlineRadio3">superiori</label>
    </div>

    <div class="form-group">
        <label for="studenti">Classi</label>
        <select class="form-control" id="studenti" wire:model="selectedStudent">
            @foreach ($classiOption as $classe)
                <option wire:key="{{ $classe->id }}" value="{{ $classe->id }}">{{ $classe->tipo->nome }}</option>
            @endforeach
        </select>
    </div>


    <div class="form-group">
        <label for="studenti">Studenti ({{count($students)}})</label>
        <select class="form-control" id="studenti" wire:model="selectedStudent">
            @foreach ($students as $student)
                <option wire:key="{{ $student->id }}" value="{{ $student->id }}">{{ $student->nominativo }}</option>
            @endforeach
        </select>
    </div>
</div>
