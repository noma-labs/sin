<div>
    <h1>Hello World!</h1>
    <div style="text-align: center">
        <p>Searching: {{$term}}</p>
        <input wire:model.debounce.500ms="term" type="text">
<!--        <button wire:click="search"></button>--}}-->
    </div>
</div>
