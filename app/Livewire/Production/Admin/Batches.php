<?php

namespace App\Livewire\Production\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.admin')]
#[Title('Jewellery Production Batches')]
class Batches extends Component
{
    public function render()
    {
        return view('livewire.production.admin.batches');
    }
}
