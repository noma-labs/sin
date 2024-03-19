<div>
    <p> Search: {{$searchTerm}}</p>
<input type="text" wire:model.live.debounce.100ms="searchTerm">


Persone:
<ul>
@foreach($people as $person)
    <li wire:key="{{ $person->id }}">{{ $person->nominativo}}
        <button type="button" wire:click="add({{$person->id}})">Add</button>
    </li>
@endforeach
</ul>


Selected :
<ul>
@foreach($selected as $s)
    <li wire:key="{{ $s->id }}">{{ $s->nominativo}}
        <button type="button" wire:click="remove({{$s->id}})" wire:confirm="Are you sure">Remove</button>
    </li>
@endforeach
</ul>

</div>
