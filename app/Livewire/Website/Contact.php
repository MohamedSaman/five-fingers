<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.website')]
#[Title('Contact & Bespoke Inquiries - Five Fingers Gem & Jewellery')]
class Contact extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $message = '';
    public $submitted = false;

    public function submitInquiry()
    {
        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.website.contact');
    }
}
