<?php

namespace App\Http\Livewire;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Livewire\Component;

class Display extends Component
{
    public array $list;

    public array $exchanges;

    public array $exchangesSelected;

    public string $selected;

    public bool $showDataLabels = false;

    public bool $firstRun = true;

    public function mount()
    {
        $this->list = ['BTC', 'ETH'];
        $this->selected = $this->list[0];
        $this->exchanges = ['Binance', 'Kucoin', 'Bitfinex'];
        $this->exchangesSelected = $this->exchanges;
    }

//    public function hydrateSelected($value)
//    {
//        dump($value);
//    }

//    public function updatedSelected($value)
//    {
//        //do stuff
//    }

    public function render()
    {
        $data = collect([
            ['exchange' => 'Binance', 'day' => 'monday', 'crypto' => 'BTC', 'price' => 75],
            ['exchange' => 'Binance', 'day' => 'tuesday', 'crypto' => 'BTC', 'price' => 85],
            ['exchange' => 'Kucoin', 'day' => 'monday', 'crypto' => 'BTC', 'price' => 76],
            ['exchange' => 'Kucoin', 'day' => 'tuesday', 'crypto' => 'BTC', 'price' => 88],
            ['exchange' => 'Binance', 'day' => 'monday', 'crypto' => 'ETH', 'price' => 25],
            ['exchange' => 'Binance', 'day' => 'tuesday', 'crypto' => 'ETH', 'price' => 22],
            ['exchange' => 'Kucoin', 'day' => 'monday', 'crypto' => 'ETH', 'price' => 21],
            ['exchange' => 'Kucoin', 'day' => 'tuesday', 'crypto' => 'ETH', 'price' => 18],
        ]);

        $prices = $data->where('crypto', $this->selected)
            ->whereIn('exchange', $this->exchangesSelected);

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
