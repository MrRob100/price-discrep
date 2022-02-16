<div>
    <div class="space-y-4 sticky top-0 bg-white p-4 shadow z-50">
        <select wire:model="selected">
            @foreach($list as $symbol)
                <option>{{ $symbol }}</option>
            @endforeach
        </select>
        <div>Crypto: {{ $selected }}</div>
        <ul class="flex flex-col sm:flex-row sm:space-x-8 sm:items-center">
            @foreach($exchanges as $exchange)
            <li>
                <input type="checkbox" id="{{ $exchange }}" value="{{ $exchange }}" wire:model="types"/>
                <label for="{{ $exchange }}">{{ $exchange }}</label>
            </li>
            @endforeach
        </ul>
        <div>
            <input type="checkbox" value="other" wire:model="showDataLabels"/>
            <span>Show data labels</span>
        </div>
    </div>

    <div class="container mx-auto space-y-4 p-4 sm:p-0 mt-8" style="height:500px">
        <livewire:livewire-line-chart
            key="{{ $lineChartModel->reactiveKey() }}"
            :line-chart-model="$lineChartModel"
        />
    </div>
</div>
