<?php

namespace App\Livewire\Services;

use Livewire\Component;

use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title('N-xotech | Service')]
#[Layout('layouts.main')]
class Service extends Component
{
    public function render()
    {
        return view('livewire.services.service');
    }
}
