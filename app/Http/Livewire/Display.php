<?php

namespace App\Http\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Display extends Component
{
    public Collection $list;
    public string $selected;

    public function mount()
    {
        $this->list = collect(['BTC', 'ETH']);
        $this->selected = $this->list->first();
    }

    public function render()
    {
        return view('livewire.display');
    }
}
