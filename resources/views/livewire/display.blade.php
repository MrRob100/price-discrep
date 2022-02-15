<div>
    <button wire:click="increment">+</button>
    <h1>{{ $count }}</h1>
    <select>
        @foreach($symbols as $symbol)
            <option>{{ $symbol }}</option>
        @endforeach
    </select>
</div>
