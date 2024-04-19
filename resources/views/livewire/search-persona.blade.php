<div>
    <style>
        .selected-option {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            line-height: 1.42857143;
            border-radius: 4px;
            margin: 4px 2px 0px 2px;
            padding: 0 0.25em;
            padding-top: 0.25em;
        }

        .available-option {
            list-style-type: none;
            margin: 10px;
        }

        .available-option:hover {
            background-color: lightblue;
            color: white; /* Change the text color to white for better contrast */
            border-radius: 4px;
            padding: 0 0.25em;
            cursor: pointer;
        }

        .my-dropdown-menu {
            position: absolute;
            background: white;
            z-index: 1000;
            width: 100%;
            padding: 0px;
            overflow-y: scroll;
            /* border: 1px solid #ccc; */
        }
    </style>
    <div
        class="form-control"
        style="display: flex; justify-content: space-between"
    >
        <div>
            @foreach ($selected as $alias)
                <span class="selected-option">
                    {{ $alias }}
                    <span
                        style="font-size: 1.25em"
                        wire:key="{{ $alias }}"
                        wire:click="deselect('{{ $alias }}')"
                    >
                        &times;
                    </span>

                    <input
                        id="cliente"
                        type="hidden"
                        name="aliases[]"
                        value="{{ $alias }}"
                    />
                </span>
            @endforeach

            <input
                style="border: none; outline: none"
                class="search"
                wire:model.live.debounce.100ms="searchTerm"
                placeholder="{{ $placeholder }}"
            />
        </div>
        <div>
            @if ($selected)
                <span wire:click="clear">&times;</span>
            @endif
        </div>
    </div>

    <ul class="my-dropdown-menu">
        @forelse ($options as $p)
            <li
                class="available-option"
                wire:key="{{ $p->alias }}"
                wire:click="select('{{ $p->alias != "" ?  $p->alias : $p->nominativo }}')"
            >
            @if ($p->alias != "")
                {{ $p->alias }}
            @else
                {{ $p->nominativo }}
            @endif
             ({{ $p->nome }} {{ $p->cognome }} {{ $p->data_nascita }})
            </li>
        @empty
            @if ($searchTerm)
                <li class="available-option">
                    {{ $noResultsMessage }}
                </li>
            @endif
        @endforelse
    </ul>
</div>
