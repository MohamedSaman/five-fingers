<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.website')]
#[Title('About Our Heritage - Five Fingers Gem & Jewellery')]
class About extends Component
{
    public function render()
    {
        return view('livewire.website.about');
    }
}
