<div>
    <h1>Hello World!</h1>
    <div style="text-align: center">
        <button wire:click="increment">+</button>
        <p> Searching anno {{$annoSearch}}</p>
        <input wire:model="annoSearch" type="text">
        <h1>{{ $count }}</h1>
{{--        <button wire:click="search"></button>--}}
    </div>
</div>
