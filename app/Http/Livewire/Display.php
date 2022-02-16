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
        $prices = collect([
            ['exchange' => 'binance', 'day' => 'monday', 'price' => 75],
            ['exchange' => 'binance', 'day' => 'tuesday', 'price' => 85],
            ['exchange' => 'kucoin', 'day' => 'monday', 'price' => 76],
            ['exchange' => 'kucoin', 'day' => 'tuesday', 'price' => 88],
        ]);

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
