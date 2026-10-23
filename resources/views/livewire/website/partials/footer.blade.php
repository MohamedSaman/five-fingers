<footer class="mt-5 pt-5" style="background: #07080a; border-top: 1px solid var(--ff-border);">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('images/five-fingers-logo.png') }}" alt="Five Fingers Logo" style="height: 52px; width: auto;">
                    <div>
                        <h5 class="brand-font m-0 gold-gradient-text fw-bold">FIVE FINGERS</h5>
                        <small class="text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.15em; color: var(--ff-gold);">Gem and Jewellery</small>
                    </div>
                </div>
                <p class="small text-secondary pe-lg-4">
                    Handcrafted luxury jewellery and premium certified gemstones. Delivering timeless elegance and exceptional quality.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="text-warning fs-5"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-warning fs-5"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="text-warning fs-5"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="brand-font text-warning mb-3">Quick Links</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2">
                    <li><a href="{{ route('website.home') }}" class="text-secondary text-decoration-none hover-gold">Home</a></li>
                    <li><a href="{{ route('website.shop') }}" class="text-secondary text-decoration-none hover-gold">Our Collection</a></li>
                    <li><a href="{{ route('website.about') }}" class="text-secondary text-decoration-none hover-gold">About Heritage</a></li>
                    <li><a href="{{ route('website.contact') }}" class="text-secondary text-decoration-none hover-gold">Contact Us</a></li>
                    <li><a href="{{ route('login') }}" class="text-warning text-decoration-none hover-gold"><i class="bi bi-person-lock me-1"></i> Staff Portal</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6 col-6">
                <h6 class="brand-font text-warning mb-3">Collections</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2">
                    <li><a href="{{ route('website.shop') }}" class="text-secondary text-decoration-none">Precious Gemstones</a></li>
                    <li><a href="{{ route('website.shop') }}" class="text-secondary text-decoration-none">Diamond Rings</a></li>
                    <li><a href="{{ route('website.shop') }}" class="text-secondary text-decoration-none">Gold Necklaces</a></li>
                    <li><a href="{{ route('website.shop') }}" class="text-secondary text-decoration-none">Custom Orders</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="brand-font text-warning mb-3">Showroom & Inquiries</h6>
                <ul class="list-unstyled small text-secondary d-flex flex-column gap-2">
                    <li class="d-flex gap-2"><i class="bi bi-geo-alt text-warning"></i> <span>Sri Lanka</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-telephone text-warning"></i> <span>+94 77 123 4567</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-envelope text-warning"></i> <span>info@fivefingers.lk</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-clock text-warning"></i> <span>Mon - Sat: 9:00 AM - 7:00 PM</span></li>
                </ul>
            </div>
        </div>

        <div class="border-top mt-4 pt-3 text-center small text-secondary" style="border-color: rgba(255,255,255,0.06) !important;">
            &copy; {{ date('Y') }} Five Fingers Gem & Jewellery. All Rights Reserved.
        </div>
    </div>
</footer>
