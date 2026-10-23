<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.website')]
#[Title('Shopping Bag - Five Fingers Gem & Jewellery')]
class Cart extends Component
{
    public function render()
    {
        return view('livewire.website.cart');
    }
}
