<?php

namespace App\Livewire\Production\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.staff')]
#[Title('Artisan Workshop Dashboard - Production')]
class StaffDashboard extends Component
{
    public function render()
    {
        return view('livewire.production.staff.dashboard');
    }
}
