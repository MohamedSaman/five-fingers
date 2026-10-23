<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.guest')]
#[Title('Sign In - Five Fingers')]
class CustomLogin extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $selectedModule = 'inventory'; // 'inventory' or 'production'

    public function setModule($module)
    {
        $this->selectedModule = $module;
    }

    public function render()
    {
        return view('livewire.custom-login');
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user && Hash::check($this->password, $user->password)) {
            // Check if user has access to the selected module
            if (!$user->hasModuleAccess($this->selectedModule)) {
                $this->addError('email', "This account does not have access to the " . ucfirst($this->selectedModule) . " module.");
                $this->password = '';
                return;
            }

            Auth::login($user, $this->remember);
            session()->put('active_module', $this->selectedModule);
            session()->regenerate();

            // Redirect based on selected module & role
            if ($this->selectedModule === 'production') {
                session()->flash('status', 'Logged into Jewelry Production Module');
                return redirect()->route('production.products');
            } else {
                // Inventory module
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } else if ($user->role === 'staff') {
                    return redirect()->route('staff.dashboard');
                } else {
                    return redirect()->route('dashboard');
                }
            }
        }

        $this->addError('email', 'These credentials do not match our records.');
        $this->password = '';
    }
}
