<div class="container py-5">
    <h2 class="gold-gradient-text fw-bold mb-4">Secure Checkout</h2>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="luxury-card p-4">
                <h5 class="brand-font text-warning mb-4">Delivery & Contact Details</h5>
                <form wire:submit.prevent="placeOrder">
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Full Name</label>
                        <input type="text" class="form-control bg-dark border-secondary text-light" wire:model="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Contact Phone / WhatsApp</label>
                        <input type="tel" class="form-control bg-dark border-secondary text-light" wire:model="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Delivery Address</label>
                        <textarea class="form-control bg-dark border-secondary text-light" rows="3" wire:model="address" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary small">Special Instructions or Custom Ring Size</label>
                        <textarea class="form-control bg-dark border-secondary text-light" rows="2" wire:model="notes"></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold btn-lg w-100">
                        Confirm Order & Inquire
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="luxury-card p-4">
                <h5 class="brand-font text-warning mb-3">Order Summary</h5>
                <p class="text-secondary small">Your luxury package will be dispatched with full tamper-proof insured courier delivery.</p>
                <div class="p-3 rounded-3" style="background: rgba(0,0,0,0.3); border: 1px solid var(--ff-border);">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-shield-check text-warning fs-3"></i>
                        <small class="text-secondary">Certified authenticity, verified gold purity, and 100% genuine gemstones guaranteed.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
