<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.website')]
#[Title('Checkout - Five Fingers Gem & Jewellery')]
class Checkout extends Component
{
    public $customer_name = '';
    public $phone = '';
    public $address = '';
    public $notes = '';

    public function render()
    {
        return view('livewire.website.checkout');
    }
}
