<div>
    <div class="form-group">
        <label for="annoScolastico">Anno Scolastico</label>
        <select
            class="form-control"
            id="annoScolastico"
            wire:change="selectAnnoScolastico($event.target.value)"
        >
            @foreach ($anni as $anno)
                @if ($selectedAnnoScolastico->scolastico == $anno->scolastico)
                    <option value="{{ $anno->id }}" selected>
                        {{ $anno->scolastico }}
                    </option>
                @else
                    <option value="{{ $anno->id }}">
                        {{ $anno->scolastico }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>

    <div>Filtri:</div>
    <div class="form-check form-check-inline">
        <input
            class="form-check-input"
            type="checkbox"
            id="inlineCheckbox3"
            wire:click="toggleSelectAll($event.target.value)"
        />
        <label class="form-check-label" for="inlineCheckbox3">
            Elimina filtri
        </label>
    </div>

    @if ($selectedAnnoScolastico)
        <div class="form-check form-check-inline">
            <input
                class="form-check-input"
                type="checkbox"
                id="inlineCheckbox1"
                wire:model.live="selectedCicloOption"
                value="prescuola"
            />
            <label class="form-check-label" for="inlineCheckbox1">
                Prescuola
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input
                class="form-check-input"
                type="checkbox"
                id="inlineCheckbox1"
                wire:model.live="selectedCicloOption"
                value="elementari"
            />
            <label class="form-check-label" for="inlineCheckbox1">
                Elementari
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input
                class="form-check-input"
                type="checkbox"
                id="inlineCheckbox2"
                wire:model.live="selectedCicloOption"
                value="medie"
            />
            <label class="form-check-label" for="inlineCheckbox2">Medie</label>
        </div>
        <div class="form-check form-check-inline">
            <input
                class="form-check-input"
                type="checkbox"
                id="inlineCheckbox3"
                wire:model.live="selectedCicloOption"
                value="superiori"
            />
            <label class="form-check-label" for="inlineCheckbox3">
                Superiori
            </label>
        </div>

        <div>Classe:</div>
        @foreach ($options as $id => $nome)
            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="inlineCheckbox1"
                    wire:key="{{ $id }}"
                    wire:model.live="selectedClassiOption"
                    value="{{ $id }}"
                />
                <label class="form-check-label" for="inlineCheckbox1">
                    {{ $nome }}
                </label>
            </div>
        @endforeach
    @endif

    <div class="form-group">
        <label name="xCategoria" for="studenti">
            Studenti ({{ count($students) }})
        </label>
        <ul>
            @foreach ($students as $student)
                <li>
                    {{ $student->nominativo }} {{ $student->ciclo }}
                    {{ $student->nome }}
                </li>
                <input
                    type="hidden"
                    name="students[]"
                    value="{{ $student->id }}"
                />
            @endforeach
        </ul>
    </div>
</div>
