<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\ProductDetail;

#[Layout('components.layouts.website')]
#[Title('Shop Collections - Five Fingers Gem & Jewellery')]
class Shop extends Component
{
    public $search = '';
    public $category = '';

    public function render()
    {
        $products = ProductDetail::query()
            ->when($this->search, fn($q) => $q->where('product_name', 'like', '%' . $this->search . '%'))
            ->paginate(12);

        return view('livewire.website.shop', [
            'products' => $products
        ]);
    }
}
