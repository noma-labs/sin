<div>
    <div
        class="form-control"
        style="display: flex; justify-content: space-between"
    >
        <div>
            @if ($selected)
                <span>{{ $selected->nominativo }}</span>
                <input
                    id="cliente"
                    type="hidden"
                    name="{{ $inputName }}"
                    value="{{ $selected->id }}"
                />
            @else
                <input
                    style="border: none; outline: none"
                    class="search"
                    wire:model.live.debounce.100ms="searchTerm"
                    placeholder="{{ $placeholder }}"
                />
            @endif
        </div>
        <div>
            @if ($selected)
                <span wire:click="clear">X</span>
            @endif
        </div>
    </div>
    <ul
        style="
            position: absolute;
            background: white;
            z-index: 1000;
            width: 100%;
            padding: 0px;
            overflow-y: scroll;
        "
    >
        @forelse ($people as $person)
            <li
                style="list-style-type: none; margin: 10px"
                wire:key="{{ $person->id }}"
                wire:click="select({{ $person->id }})"
            >
                {{ $person->nominativo }}
            </li>
        @empty
            @if ($selected == null && $searchTerm)
                <li
                    style="
                        list-style-type: none;
                        margin: 10px;
                        text-align: center;
                    "
                >
                    Nessun risultato
                </li>
            @endif
        @endforelse
    </ul>
</div>
