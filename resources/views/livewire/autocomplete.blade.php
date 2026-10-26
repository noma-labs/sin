<div>
  <div class="form-control h-auto">
    <div>
      @foreach ($selected as $sel)
        <span class="selected-option" wire:key="selected-{{ $sel->id }}">
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
          class="search"
          wire:model.live.debounce.100ms="searchTerm"
          placeholder="{{ $placeholder }}"
        />
      @endif
    </div>
  </div>

  <ul class="my-dropdown-menu">
    @forelse ($options as $p)
      <li
        class="available-option"
        wire:key="option-{{ $p->id }}"
        wire:click="select('{{ $p->id }}')"
      >
        {{ $p->value }}
      </li>
    @empty
      @if ($searchTerm)
        <li class="available-option">{{ $noResultsMessage }}</li>
      @endif
    @endforelse
  </ul>
</div>
