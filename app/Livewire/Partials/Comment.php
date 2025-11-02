<?php

namespace App\Livewire\Partials;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.main')]
class Comment extends Component
{
    public function render()
    {
        return view('livewire.partials.comment');
    }
}
