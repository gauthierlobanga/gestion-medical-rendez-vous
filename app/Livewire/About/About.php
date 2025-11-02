<?php

namespace App\Livewire\About;

use Livewire\Component;

use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.main')]
#[Title('N-xotech | A-propos')]
class About extends Component
{
    public function render()
    {
        return view('livewire.about.about');
    }
}
