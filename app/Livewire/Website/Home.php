<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.website')]
#[Title('Five Fingers - Gem & Jewellery Luxury Collection')]
class Home extends Component
{
    public function render()
    {
        return view('livewire.website.home');
    }
}
