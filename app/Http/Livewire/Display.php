<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Display extends Component
{
    public $list = ['OTH', 'COI'];

    public function render()
    {
        return view('livewire.display')->with('symbols', ['BTC', 'ETH']);
    }
}
