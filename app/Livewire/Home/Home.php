<?php

namespace App\Livewire\Home;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.main')]
class Home extends Component
{

    public function toJSON()
    {
        return json_encode([]);
    }

    public function render()
    {
        return view('livewire.home.home');
    }
}
