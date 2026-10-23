<nav class="navbar navbar-expand-lg navbar-dark sticky-top" style="background: rgba(11, 12, 16, 0.92); backdrop-filter: blur(12px); border-bottom: 1px solid var(--ff-border); z-index: 1030;">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('website.home') }}">
            <img src="{{ asset('images/five-fingers-logo.png') }}" alt="Five Fingers" style="height: 48px; width: auto; object-fit: contain;">
            <div class="d-none d-sm-block">
                <span class="brand-font fw-bold d-block gold-gradient-text" style="font-size: 1.15rem; line-height: 1.1;">FIVE FINGERS</span>
                <small class="text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.2em; color: var(--ff-gold-light);">Gem & Jewellery</small>
            </div>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#websiteNav">
            <i class="bi bi-list fs-2 text-warning"></i>
        </button>

        <div class="collapse navbar-collapse" id="websiteNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.home') ? 'active text-warning fw-semibold' : 'text-light' }}" href="{{ route('website.home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.shop') ? 'active text-warning fw-semibold' : 'text-light' }}" href="{{ route('website.shop') }}">Collection / Shop</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.about') ? 'active text-warning fw-semibold' : 'text-light' }}" href="{{ route('website.about') }}">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('website.contact') ? 'active text-warning fw-semibold' : 'text-light' }}" href="{{ route('website.contact') }}">Contact & Inquiry</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('website.cart') }}" class="btn btn-outline-gold position-relative d-flex align-items-center gap-2">
                    <i class="bi bi-bag"></i>
                    <span>Cart</span>
                    <span class="badge bg-warning text-dark rounded-pill">0</span>
                </a>
                <a href="{{ url('/dashboard') }}" class="btn btn-gold d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Portal</span>
                </a>
            </div>
        </div>
    </div>
</nav>
