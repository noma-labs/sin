<div>
<p> Search: {{$searchTerm}}</p>
<!-- <form wire:submit="search"> -->
    <input type="text" wire:model.live.debounce.100ms="searchTerm">
    <!-- <input type="text" wire:model="searchTerm"> -->
    <!-- <button type="submit">Search</button> -->
<!-- </form> -->

Persone:
<ul>
@foreach($people as $person)
    <li>{{ $person['nominativo'] }}</li>
@endforeach
</ul>

</div>
