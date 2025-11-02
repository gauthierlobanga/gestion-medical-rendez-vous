<?php

namespace App\Livewire\Support;

use Livewire\Component;

use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.main')]
#[Title('N-xotechh | Aide')]
class Aide extends Component
{
    public function render()
    {
        return view('livewire.support.aide');
    }
}
