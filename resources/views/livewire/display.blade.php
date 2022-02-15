<div>
    <select wire:model="selected">
        @foreach($list as $symbol)
            <option>{{ $symbol }}</option>
        @endforeach
    </select>
    <div>Crypto: {{ $selected }}</div>
</div>
