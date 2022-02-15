<div>
    <select>
        @foreach($symbols as $symbol)
            <option>{{ $symbol }}</option>
        @endforeach
    </select>
    <select>
        @foreach($list as $item)
            <option>{{ $item }}</option>
        @endforeach
    </select>
</div>
