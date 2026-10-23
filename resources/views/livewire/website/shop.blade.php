<div class="container py-5">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="gold-gradient-text fw-bold m-0">Fine Jewellery & Gem Collection</h2>
            <p class="text-secondary small m-0">Handcrafted masterpieces and authenticated natural stones</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0">
            <div class="input-group">
                <span class="input-group-text bg-dark border-secondary text-warning"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control bg-dark border-secondary text-light" placeholder="Search by gem, ring, bracelet, necklace..." wire:model.live.debounce.300ms="search">
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="luxury-card h-100 p-3 d-flex flex-column">
                    <div class="text-center p-3 rounded-3 mb-3" style="background: rgba(0,0,0,0.4); height: 180px; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('images/five-fingers-logo.png') }}" alt="{{ $product->product_name }}" style="max-height: 120px; max-width: 100%; object-fit: contain;">
                    </div>
                    <div class="flex-grow-1">
                        <small class="text-warning text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.1em;">Five Fingers Exclusive</small>
                        <h6 class="text-light fw-bold mt-1 mb-2">{{ $product->product_name }}</h6>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary">
                        <span class="text-warning fw-bold fs-6">Rs. {{ number_format($product->selling_price ?? 0, 2) }}</span>
                        <a href="{{ route('website.product-detail', $product->id) }}" class="btn btn-sm btn-outline-gold">
                            View Piece
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-gem text-warning fs-1 mb-3 d-block"></i>
                <h5 class="text-light">No jewellery pieces found matching your criteria</h5>
                <p class="text-secondary small">Please adjust your search or contact us for custom commissions.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
