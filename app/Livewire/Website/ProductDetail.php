<?php

namespace App\Livewire\Website;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\ProductDetail as ProductDetailModel;

#[Layout('components.layouts.website')]
#[Title('Product Details - Five Fingers Gem & Jewellery')]
class ProductDetail extends Component
{
    public $product;

    public function mount($id)
    {
        $this->product = ProductDetailModel::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.website.product-detail');
    }
}
