<?php

namespace App\Http\Livewire;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
        $this->list = [
            'BTC',
            'ETH',
            'BNB',
            'XRP',
            'DOT',
            'ADA',
            'SOL',
            'AVAX',
            'LUNA',
            'DOGE',
            'DOT',
            'SHIB',
            'MATIC',
            'CRO',
        ];
        $this->selected = $this->list[0];
        $this->exchanges = ['Binance', 'Kucoin', 'Bitfinex'];
        $this->exchangesSelected = $this->exchanges;
    }

    public function render()
    {
        $raw = collect([]);
        $raw->push($this->getBinanceData($this->selected));
        $raw->push($this->getKucoinData($this->selected));
        $raw->push($this->getBitfinexData($this->selected));

        $prices = $raw->flatten(1)->where('crypto', $this->selected)
            ->where('day', Carbon::now()->day)
            ->whereIn('exchange', $this->exchangesSelected);

        $multiLineChartModel = $prices->reduce(function ($multiLineChartModel, $data) use ($prices) {
            $index = $prices->search($data);
            return $multiLineChartModel->addSeriesPoint($data['exchange'], $index, $data['price']);
        }, LivewireCharts::multiLineChartModel()
            ->setTitle("Hourly price discrepancies for $this->selected today")
            ->setAnimated($this->firstRun)
            ->withOnPointClickEvent('onPointClick')
            ->setSmoothCurve()
            ->multiLine()
            ->withLegend()
            ->setXAxisVisible(true)
            ->setDataLabelsEnabled($this->showDataLabels)
            ->sparklined()
            ->setColors(['#3490dc', '#38c172', '#000'])
        );

        $this->firstRun = false;

        return view('livewire.display')
            ->with([
                'lineChartModel' => $multiLineChartModel,
            ]);
    }

    public function getBinanceData($symbol) {
        if (Cache::has("$symbol-binance")) {
            $data = Cache::get("$symbol-binance");
        } else {
            $startTime = Carbon::now()->startOfDay()->unix() * 1000;
            $response = json_decode(file_get_contents("https://www.binance.com/api/v3/klines?symbol={$symbol}USDT&interval=1h&startTime={$startTime}"), true);

            $data = collect($response)->map(function($item) use ($symbol) {
                $date = Carbon::createFromTimestamp($item[0] / 1000);
                return [
                    'hour' => $date->format('d/m/Y:H'),
                    'day' => $date->day,
                    'price' => $item[2],
                    'exchange' => 'Binance',
                    'crypto' => $symbol,
                ];
            });

            Cache::put("$symbol-binance", $data, 600);
        }

        return $data;
    }

    public function getKucoinData($symbol) {
        if (Cache::has("$symbol-kucoin")) {
            $data = Cache::get("$symbol-kucoin");
        } else {
            $startTime = Carbon::now()->startOfDay()->unix();
            $response = json_decode(file_get_contents("https://api.kucoin.com/api/v1/market/candles?type=1hour&symbol={$symbol}-USDT&startAt={$startTime}"), true);
            $data = collect($response['data'])->map(function($item) use ($symbol) {
                $date = Carbon::createFromTimestamp(intval($item[0]));
                return [
                    'hour' => $date->format('d/m/Y:H'),
                    'day' => $date->day,
                    'price' => $item[2],
                    'exchange' => 'Kucoin',
                    'crypto' => $symbol,
                ];
            });

            Cache::put("$symbol-kucoin", $data, 600);
        }

        return $data;
    }

    public function getBitfinexData($symbol) {
        if (Cache::has("$symbol-bitfinex")) {
            $data = Cache::get("$symbol-bitfinex");
        } else {
            $response = array_reverse(json_decode(file_get_contents("https://api-pub.bitfinex.com/v2/candles/trade:1h:t{$symbol}USD/hist")));
            $data = collect($response)->map(function ($item) use ($symbol) {
                $date = Carbon::createFromTimestamp($item[0] / 1000);
                return [
                    'hour' => $date->format('d/m/Y:H'),
                    'day' => $date->day,
                    'price' => $item[2],
                    'exchange' => 'Bitfinex',
                    'crypto' => $symbol,
                ];
            });

            Cache::put("$symbol-bitfinex", $data, 600);
        }

        return $data;
    }
}
