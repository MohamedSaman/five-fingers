<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Five Fingers jewellery manufacturing — production lifecycle, metal loss and cost tracking.">

    <title>{{ $title ?? 'Production' }} · Five Fingers</title>
    <link rel="icon" type="image/png" href="{{ asset('images/five-fingers-logo.png') }}">

    <meta name="color-scheme" content="dark light">
    <script>
        (function() {
            var savedTheme = localStorage.getItem('production-theme');
            var theme = savedTheme ? savedTheme : 'dark';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=JetBrains+Mono:wght@500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/production.css') }}?v={{ filemtime(public_path('css/production.css')) }}">

    @livewireStyles
    @stack('styles')
</head>

@php
    $fullWidth = $fullWidth ?? false;
    $routeName = request()->route()?->getName() ?? '';
    $section = match (true) {
        str_starts_with($routeName, 'production.expenses') => 'expenses',
        str_starts_with($routeName, 'production.vault') => 'vault',
        default => 'products',
    };
    $qStatus = request('status');
    $qType = request('type');
    $qTab = request('tab', 'bullion');
@endphp

<body class="pd" x-data="{
    theme: document.documentElement.getAttribute('data-theme') || 'dark',
    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', this.theme);
        localStorage.setItem('production-theme', this.theme);
    }
}">

    <!-- ============ TOP BAR ============ -->
    <header class="pd-top">
        <a href="{{ route('production.products') }}" class="pd-brand" aria-label="Five Fingers production home">
            <img src="{{ asset('images/five-fingers-logo.png') }}" alt="">
            <div>
                <div class="pd-brand-name">FIVE FINGERS</div>
                <div class="pd-brand-sub">Atelier · Production</div>
            </div>
        </a>

        <nav class="pd-seg" aria-label="Sections">
            <a href="{{ route('production.products') }}" class="{{ $section === 'products' ? 'is-active' : '' }}">
                <i class="bi bi-gem"></i><span>Products</span>
            </a>
            <a href="{{ route('production.expenses') }}" class="{{ $section === 'expenses' ? 'is-active' : '' }}">
                <i class="bi bi-wallet2"></i><span>Expenses</span>
            </a>
            <a href="{{ route('production.vault') }}" class="{{ $section === 'vault' ? 'is-active' : '' }}">
                <i class="bi bi-safe2"></i><span>Purchasing</span>
            </a>
        </nav>

        <div class="pd-top-right">
            <div class="pd-ticker" title="Reference spot price">
                <span class="live-dot"></span>
                <span>24K <b class="tnum">Rs 24,500</b>/g</span>
                <span class="sep"></span>
                <span>18K <b class="tnum">Rs 18,375</b>/g</span>
            </div>

            <!-- Theme Toggle Button -->
            <button type="button" class="icon-btn" id="btn-theme-toggle"
                    @click="toggleTheme()"
                    :title="theme === 'dark' ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
                    aria-label="Toggle color theme">
                <i class="bi" :class="theme === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                   :style="theme === 'dark' ? 'color: var(--gold)' : 'color: var(--blue)'"></i>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm" title="Switch to Inventory & Sales">
                <i class="bi bi-arrow-left-right"></i> Inventory
            </a>

            <div class="pd-user" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
                <button type="button" class="pd-user-btn" @click="open = !open" :aria-expanded="open">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</span>
                    <span style="font-size:13px;font-weight:600">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <i class="bi bi-chevron-down dim" style="font-size:11px"></i>
                </button>
                <div class="pd-menu" x-show="open" x-transition.opacity.duration.150ms x-cloak>
                    <div class="pd-menu-head">
                        <div style="font-weight:700">{{ auth()->user()->name ?? '' }}</div>
                        <div class="dim" style="font-size:12px">{{ auth()->user()->email ?? '' }}</div>
                    </div>
                    <a href="{{ route('profile.show') }}"><i class="bi bi-person"></i> Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="danger"><i class="bi bi-box-arrow-right"></i> Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- ============ SHELL ============ -->
    <div class="pd-shell {{ $fullWidth ? 'is-full' : '' }}">

        @unless($fullWidth)
            <aside class="pd-side" aria-label="Section menu">
                @if($section === 'products')
                    @php
                        $cAll = \App\Models\ProductionProduct::count();
                        $cProd = \App\Models\ProductionProduct::where('status', 'in_production')->count();
                        $cQc = \App\Models\ProductionProduct::where('status', 'qc_review')->count();
                        $cDone = \App\Models\ProductionProduct::where('status', 'completed')->count();
                    @endphp
                    <div class="pd-side-label">Jobs</div>
                    <a href="{{ route('production.products') }}" class="pd-side-link {{ !$qStatus ? 'is-active' : '' }}">
                        <i class="bi bi-grid"></i> All jobs <span class="count">{{ $cAll }}</span>
                    </a>
                    <a href="{{ route('production.products', ['status' => 'in_production']) }}" class="pd-side-link {{ $qStatus === 'in_production' ? 'is-active' : '' }}">
                        <i class="bi bi-hourglass-split"></i> In production <span class="count">{{ $cProd }}</span>
                    </a>
                    <a href="{{ route('production.products', ['status' => 'qc_review']) }}" class="pd-side-link {{ $qStatus === 'qc_review' ? 'is-active' : '' }}">
                        <i class="bi bi-shield-exclamation"></i> QC review <span class="count">{{ $cQc }}</span>
                    </a>
                    <a href="{{ route('production.products', ['status' => 'completed']) }}" class="pd-side-link {{ $qStatus === 'completed' ? 'is-active' : '' }}">
                        <i class="bi bi-patch-check"></i> Completed <span class="count">{{ $cDone }}</span>
                    </a>

                    <div class="pd-side-label" style="margin-top:10px">Workshop</div>
                    <a href="{{ route('production.products', ['stage' => 1]) }}" class="pd-side-link"><i class="bi bi-bezier2"></i> CAD design</a>
                    <a href="{{ route('production.products', ['stage' => 8]) }}" class="pd-side-link"><i class="bi bi-fire"></i> Casting</a>
                    <a href="{{ route('production.products', ['stage' => 11]) }}" class="pd-side-link"><i class="bi bi-tools"></i> Bench work</a>
                    <a href="{{ route('production.products', ['stage' => 12]) }}" class="pd-side-link"><i class="bi bi-gem"></i> Stone setting</a>
                    <a href="{{ route('production.products', ['stage' => 13]) }}" class="pd-side-link"><i class="bi bi-stars"></i> Polishing</a>

                @elseif($section === 'expenses')
                    <div class="pd-side-label">Costing</div>
                    <a href="{{ route('production.expenses') }}" class="pd-side-link {{ !$qType ? 'is-active' : '' }}"><i class="bi bi-receipt"></i> All expenses</a>
                    <a href="{{ route('production.expenses', ['type' => 'labor']) }}" class="pd-side-link {{ $qType === 'labor' ? 'is-active' : '' }}"><i class="bi bi-person-badge"></i> Artisan labour</a>
                    <a href="{{ route('production.expenses', ['type' => 'consumable']) }}" class="pd-side-link {{ $qType === 'consumable' ? 'is-active' : '' }}"><i class="bi bi-droplet"></i> Consumables</a>
                    <a href="{{ route('production.expenses', ['type' => 'metal_loss']) }}" class="pd-side-link {{ $qType === 'metal_loss' ? 'is-active' : '' }}"><i class="bi bi-graph-down-arrow"></i> Metal loss</a>

                @else
                    <div class="pd-side-label">Vault</div>
                    <a href="{{ route('production.vault', ['tab' => 'bullion']) }}" class="pd-side-link {{ $qTab === 'bullion' ? 'is-active' : '' }}"><i class="bi bi-coin"></i> Bullion</a>
                    <a href="{{ route('production.vault', ['tab' => 'stones']) }}" class="pd-side-link {{ $qTab === 'stones' ? 'is-active' : '' }}"><i class="bi bi-gem"></i> Gemstones</a>
                    <a href="{{ route('production.vault', ['tab' => 'finished']) }}" class="pd-side-link {{ $qTab === 'finished' ? 'is-active' : '' }}"><i class="bi bi-box2-heart"></i> Finished pieces</a>
                @endif

                <div class="pd-side-foot">
                    <span>v2.0</span>
                    <span class="chip green" style="height:20px"><span class="dot"></span>Online</span>
                </div>
            </aside>
        @endunless

        <main class="pd-main">
            {{ $slot }}
        </main>
    </div>

    <style>[x-cloak]{display:none!important}</style>
    @livewireScripts
    @stack('scripts')
</body>
</html>
