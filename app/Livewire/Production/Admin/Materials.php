<?php

namespace App\Livewire\Production\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Raw Materials & Gemstone Stock - Production')]
class Materials extends Component
{
    public function render()
    {
        return view('livewire.production.admin.materials');
    }
}
