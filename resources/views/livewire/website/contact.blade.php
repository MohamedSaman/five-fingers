<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="gold-gradient-text fw-bold">Contact & Bespoke Inquiries</h1>
        <p class="text-secondary">Reach out to our master gemologists and artisans for custom commissions or viewings.</p>
    </div>

    <div class="row g-5">
        <div class="col-lg-6">
            <div class="luxury-card p-4">
                <h5 class="brand-font text-warning mb-4">Send an Inquiry</h5>
                @if($submitted)
                    <div class="alert alert-success bg-dark text-warning border-warning">
                        <i class="bi bi-check-circle me-2"></i> Thank you! Our master jeweler will contact you shortly.
                    </div>
                @endif
                <form wire:submit.prevent="submitInquiry">
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Your Name</label>
                        <input type="text" class="form-control bg-dark border-secondary text-light" wire:model="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Email Address</label>
                        <input type="email" class="form-control bg-dark border-secondary text-light" wire:model="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Phone / WhatsApp</label>
                        <input type="tel" class="form-control bg-dark border-secondary text-light" wire:model="phone">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-secondary small">Message / Inquiry / Custom Request</label>
                        <textarea class="form-control bg-dark border-secondary text-light" rows="4" wire:model="message" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold btn-lg w-100">
                        Submit Inquiry
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="luxury-card p-4 h-100">
                <h5 class="brand-font text-warning mb-4">Direct Concierge</h5>
                <div class="d-flex flex-column gap-4 text-secondary">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-geo-alt-fill text-warning fs-4"></i>
                        <div>
                            <strong class="text-light d-block">Showroom Address</strong>
                            <span>Five Fingers Gem and Jewellery, Sri Lanka</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-telephone-fill text-warning fs-4"></i>
                        <div>
                            <strong class="text-light d-block">Phone Support</strong>
                            <span>+94 77 123 4567</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-whatsapp text-warning fs-4"></i>
                        <div>
                            <strong class="text-light d-block">Instant WhatsApp Concierge</strong>
                            <span>+94 77 123 4567</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-envelope-fill text-warning fs-4"></i>
                        <div>
                            <strong class="text-light d-block">Email Inquiries</strong>
                            <span>inquiries@fivefingers.lk</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
