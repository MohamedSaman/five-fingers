<div class="container py-5">
    <h2 class="gold-gradient-text fw-bold mb-4">Your Shopping Bag</h2>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="luxury-card p-4 text-center py-5">
                <i class="bi bi-bag-check text-warning fs-1 mb-3 d-block"></i>
                <h5 class="text-light">Your luxury bag is currently empty</h5>
                <p class="text-secondary small mb-4">Explore our exclusive collections of rings, sapphires, and custom handcrafted pieces.</p>
                <a href="{{ route('website.shop') }}" class="btn btn-gold">
                    <i class="bi bi-gem me-2"></i> Browse Collection
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="luxury-card p-4">
                <h5 class="brand-font text-warning mb-3">Order Summary</h5>
                <div class="d-flex justify-content-between text-secondary mb-2">
                    <span>Subtotal</span>
                    <span class="text-light">Rs. 0.00</span>
                </div>
                <div class="d-flex justify-content-between text-secondary mb-3">
                    <span>Insured Shipping</span>
                    <span class="text-success">Complimentary</span>
                </div>
                <hr class="border-secondary">
                <div class="d-flex justify-content-between fw-bold text-light fs-5 mb-4">
                    <span>Total</span>
                    <span class="text-warning">Rs. 0.00</span>
                </div>
                <a href="{{ route('website.checkout') }}" class="btn btn-gold w-100 disabled">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    </div>
</div>
