<?php

namespace App\Http\Livewire;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Collection;
use Livewire\Component;

class Display extends Component
{
    public Collection $list;

    public string $selected;

    public bool $showDataLabels = false;

    public bool $firstRun = true;

    public function mount()
    {
        $this->list = collect(['BTC', 'ETH']);
        $this->selected = $this->list->first();
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
        $lineChartModel =
            (new LineChartModel())
                ->setTitle('liney')
                ->multiLine()
                ->setAnimated($this->firstRun)
                ->addSeriesPoint('binance', 'monday', 75)
                ->addSeriesPoint('binance', 'tuesday', 77)
                ->addSeriesPoint('kucoin', 'monday', 73)
                ->addSeriesPoint('kucoin', 'tuesday', 71)
        ;

        $this->firstRun = false;

        return view('livewire.display')
            ->with([
                'lineChartModel' => $lineChartModel,
            ]);
    }
}
