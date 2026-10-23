<div class="container py-5">
    <a href="{{ route('website.shop') }}" class="btn btn-sm btn-outline-gold mb-4">
        <i class="bi bi-arrow-left me-1"></i> Back to Collection
    </a>

    <div class="row g-5">
        <div class="col-lg-6 text-center">
            <div class="luxury-card p-5">
                <img src="{{ asset('images/five-fingers-logo.png') }}" alt="{{ $product->product_name }}" class="img-fluid rounded-3" style="max-height: 380px; object-fit: contain;">
            </div>
        </div>

        <div class="col-lg-6">
            <span class="badge border border-warning text-warning px-3 py-2 rounded-pill mb-3" style="background: rgba(212,175,55,0.08);">
                Five Fingers Signature Piece
            </span>
            <h2 class="gold-gradient-text fw-bold mb-3">{{ $product->product_name }}</h2>
            <div class="fs-3 fw-bold text-warning mb-4">
                Rs. {{ number_format($product->selling_price ?? 0, 2) }}
            </div>

            <div class="luxury-card p-4 mb-4">
                <h6 class="text-warning brand-font mb-3">Specifications & Authenticity</h6>
                <div class="row g-2 small text-secondary">
                    <div class="col-6">Item Code: <span class="text-light">{{ $product->product_code ?? 'N/A' }}</span></div>
                    <div class="col-6">Certificate: <span class="text-light">Verified Genuine</span></div>
                    <div class="col-6">Craftsmanship: <span class="text-light">Handmade Luxury</span></div>
                    <div class="col-6">Availability: <span class="text-success">In Stock</span></div>
                </div>
            </div>

            <div class="d-flex gap-3">
                <a href="{{ route('website.cart') }}" class="btn btn-gold btn-lg flex-grow-1">
                    <i class="bi bi-bag-plus me-2"></i> Add to Cart
                </a>
                <a href="https://wa.me/94771234567?text=Hello%20Five%20Fingers,%20I%20am%20interested%20in%20{{ urlencode($product->product_name) }}" target="_blank" class="btn btn-outline-gold btn-lg">
                    <i class="bi bi-whatsapp"></i> Inquire
                </a>
            </div>
        </div>
    </div>
</div>
