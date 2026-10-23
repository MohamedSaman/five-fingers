<?php

namespace App\Livewire\Production\Admin;

use App\Models\ProductionProduct;
use App\Models\ProductionStone;
use Livewire\Component;

class VaultStock extends Component
{
    public $activeTab = 'bullion';

    public function mount()
    {
        $this->activeTab = request()->get('tab', 'bullion');
    }

    public function render()
    {
        $activeProducts = ProductionProduct::where('status', 'in_production')->get();
        $completedProducts = ProductionProduct::where('status', 'completed')->get();
        $allocatedStones = ProductionStone::with('product')->get();

        return view('livewire.production.admin.vault-stock', [
            'activeProducts' => $activeProducts,
            'completedProducts' => $completedProducts,
            'allocatedStones' => $allocatedStones,
        ])->layout('components.layouts.production', [
            'title' => 'Purchasing & Raw Materials Vault'
        ]);
    }
}
