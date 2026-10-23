<div>
    <!-- Hero Banner -->
    <section class="position-relative py-5 overflow-hidden" style="background: radial-gradient(circle at 50% 30%, rgba(212, 175, 55, 0.12) 0%, rgba(11, 12, 16, 1) 70%);">
        <div class="container py-lg-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge border border-warning text-warning px-3 py-2 rounded-pill mb-3" style="background: rgba(212,175,55,0.08);">
                        <i class="bi bi-gem me-1"></i> Certified Natural Gemstones & Fine Jewellery
                    </span>
                    <h1 class="display-4 fw-bold mb-3 gold-gradient-text">
                        Crafted to Perfection, Born of Luxury.
                    </h1>
                    <p class="lead text-secondary mb-4" style="font-size: 1.05rem;">
                        Discover hand-selected Ceylon sapphires, flawless diamonds, and bespoke handcrafted gold jewellery made to last generations.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('website.shop') }}" class="btn btn-gold btn-lg">
                            <i class="bi bi-gem me-2"></i> Explore Collections
                        </a>
                        <a href="{{ route('website.contact') }}" class="btn btn-outline-gold btn-lg">
                            Custom Order
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-4 p-4 luxury-card" style="border: 2px solid rgba(212,175,55,0.3); background: rgba(18,20,26,0.85);">
                            <img src="{{ asset('images/five-fingers-logo.png') }}" alt="Five Fingers Jewellery" class="img-fluid rounded-3" style="max-height: 340px; object-fit: contain;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories / Features -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="gold-gradient-text fw-bold">Signature Collections</h2>
                <p class="text-secondary">Discover our carefully curated luxury pieces</p>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="luxury-card p-4 text-center h-100">
                        <div class="fs-1 text-warning mb-3"><i class="bi bi-gem"></i></div>
                        <h5 class="fw-bold text-light mb-2">Precious Gemstones</h5>
                        <p class="small text-secondary mb-3">Certified Royal Blue Sapphires, Rubies, Emeralds and rare natural gems.</p>
                        <a href="{{ route('website.shop') }}" class="btn btn-sm btn-outline-gold">View Stones</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="luxury-card p-4 text-center h-100">
                        <div class="fs-1 text-warning mb-3"><i class="bi bi-circle"></i></div>
                        <h5 class="fw-bold text-light mb-2">Diamond & Gold Rings</h5>
                        <p class="small text-secondary mb-3">Engagement rings, wedding bands, and statement artisan gold rings.</p>
                        <a href="{{ route('website.shop') }}" class="btn btn-sm btn-outline-gold">View Rings</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="luxury-card p-4 text-center h-100">
                        <div class="fs-1 text-warning mb-3"><i class="bi bi-stars"></i></div>
                        <h5 class="fw-bold text-light mb-2">Necklaces & Pendants</h5>
                        <p class="small text-secondary mb-3">Intricate 22k & 18k handcrafted necklace chains and gemstone pendants.</p>
                        <a href="{{ route('website.shop') }}" class="btn btn-sm btn-outline-gold">View Necklaces</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="luxury-card p-4 text-center h-100">
                        <div class="fs-1 text-warning mb-3"><i class="bi bi-tools"></i></div>
                        <h5 class="fw-bold text-light mb-2">Custom Jewellery</h5>
                        <p class="small text-secondary mb-3">Bespoke 3D design and master goldsmith crafting tailored for you.</p>
                        <a href="{{ route('website.contact') }}" class="btn btn-sm btn-outline-gold">Inquire Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
