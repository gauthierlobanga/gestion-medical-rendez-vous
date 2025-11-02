<?php

namespace App\Livewire\Services;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.main')]
#[Title('N-xotech | Feature')]
class Feature extends Component
{
    public function render()
    {
        return view('livewire.services.feature');
    }
}
