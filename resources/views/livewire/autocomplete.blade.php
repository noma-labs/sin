<div class="autocomplete">
  <div class="form-control h-auto">
    <div class="autocomplete-campo">
      @foreach ($selected as $sel)
        <span class="selected-option" wire:key="selected-{{ $sel->id }}">
          @if ($sel->persona)
            <x-persona-avatar :persona="(object) $sel->persona" size="xs" />
          @endif

          {{ $sel->value }}
          <button
            type="button"
            class="deselect-option"
            aria-label="Rimuovi {{ $sel->value }}"
            wire:click="deselect('{{ $sel->id }}')"
          >
            &times;
          </button>

          <input type="hidden" name="{{ $nameInput }}" value="{{ $sel->id }}" />
        </span>
      @endforeach

      @if ($multiple || count($selected) == 0)
        <input
          style="border: none; outline: none"
          class="search autocomplete-cerca"
          wire:model.live.debounce.100ms="searchTerm"
          placeholder="{{ $placeholder }}"
        />
      @endif
    </div>
  </div>

  @if (count($options) > 0 || $searchTerm !== "")
    <ul class="my-dropdown-menu">
      @forelse ($options as $p)
        <li wire:key="option-{{ $p->id }}">
          <button
            type="button"
            class="available-option"
            wire:click="select('{{ $p->id }}')"
          >
            @if ($p->persona)
              <x-persona-avatar :persona="(object) $p->persona" size="xs" />
            @endif

            <span>{{ $p->value }}</span>
          </button>
        </li>
      @empty
        <li class="available-option-vuota">{{ $noResultsMessage }}</li>
      @endforelse
    </ul>
  @endif
</div>
