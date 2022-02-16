<?php

namespace App\Http\Livewire;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Support\Collection;
use Livewire\Component;

class Display extends Component
{
    public Collection $list;

    public Collection $exchanges;

    public string $selected;

    public bool $showDataLabels = false;

    public bool $firstRun = true;

    public function mount()
    {
        $this->list = collect(['BTC', 'ETH']);
        $this->selected = $this->list->first();
        $this->exchanges = collect(['Binance', 'Kucoin', 'Bitfinex']);
    }

//    public function hydrateSelected($value)
//    {
//        dump($value);
//    }

    public function updatedSelected($value)
    {
        //do stuff
    }

    public function render()
    {
        $data = collect([
            ['exchange' => 'binance', 'day' => 'monday', 'crypto' => 'BTC', 'price' => 75],
            ['exchange' => 'binance', 'day' => 'tuesday', 'crypto' => 'BTC', 'price' => 85],
            ['exchange' => 'kucoin', 'day' => 'monday', 'crypto' => 'BTC', 'price' => 76],
            ['exchange' => 'kucoin', 'day' => 'tuesday', 'crypto' => 'BTC', 'price' => 88],
            ['exchange' => 'binance', 'day' => 'monday', 'crypto' => 'ETH', 'price' => 25],
            ['exchange' => 'binance', 'day' => 'tuesday', 'crypto' => 'ETH', 'price' => 22],
            ['exchange' => 'kucoin', 'day' => 'monday', 'crypto' => 'ETH', 'price' => 21],
            ['exchange' => 'kucoin', 'day' => 'tuesday', 'crypto' => 'ETH', 'price' => 18],
        ]);

        $prices = $data->where('crypto', $this->selected);

        $multiLineChartModel = $prices->reduce(function ($multiLineChartModel, $data) use ($prices) {
            $index = $prices->search($data);
            return $multiLineChartModel->addSeriesPoint($data['exchange'], $index, $data['price']);
        }, LivewireCharts::multiLineChartModel()
            ->setTitle("Price discrepencies for $this->selected")
            ->setAnimated($this->firstRun)
            ->withOnPointClickEvent('onPointClick')
            ->setSmoothCurve()
            ->multiLine()
            ->setXAxisVisible(true)
            ->setDataLabelsEnabled($this->showDataLabels)
            ->sparklined()
            ->setColors(['#b01a1b', '#d41b2c', '#ec3c3b', '#f66665'])
        );

        $this->firstRun = false;

        return view('livewire.display')
            ->with([
                'lineChartModel' => $multiLineChartModel,
            ]);
    }
}
