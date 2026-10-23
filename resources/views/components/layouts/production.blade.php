<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Jewelry Production' }} - Five Fingers Gem & Jewellery</title>
    <link rel="icon" type="image/png" href="{{ asset('images/five-fingers-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Cinzel:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --gold-primary: #d4af37;
            --gold-light: #f3e5ab;
            --gold-dark: #aa820a;
            --gold-gradient: linear-gradient(135deg, #e6ca65 0%, #d4af37 50%, #997405 100%);
            --header-bg: #0d1117;
            --sidebar-bg: #161b22;
            --surface: #ffffff;
            --page-bg: #f4f6f9;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--page-bg);
            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        /* Top Luxury Navbar */
        .prod-topbar {
            background-color: var(--header-bg);
            border-bottom: 1px solid rgba(212, 175, 55, 0.25);
            height: 64px;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .prod-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .prod-brand img {
            height: 42px;
            width: auto;
            border-radius: 6px;
        }

        .prod-brand-text {
            display: flex;
            flex-direction: column;
        }

        .prod-brand-title {
            font-family: 'Cinzel', serif;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .prod-brand-badge {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--gold-primary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Top Menu Navigation Tabs */
        .prod-top-nav {
            display: flex;
            gap: 8px;
            margin: 0 20px;
        }

        .prod-top-tab {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 8px;
            color: #94a3b8;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .prod-top-tab:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.06);
        }

        .prod-top-tab.active {
            color: #ffffff;
            background: rgba(212, 175, 55, 0.15);
            border-color: rgba(212, 175, 55, 0.4);
        }

        .prod-top-tab.active i {
            color: var(--gold-primary);
        }

        /* Gold Price Ticker Widget */
        .gold-ticker {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(212, 175, 55, 0.08);
            border: 1px solid rgba(212, 175, 55, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            color: #e2e8f0;
        }

        .gold-ticker .gold-dot {
            width: 7px;
            height: 7px;
            background-color: var(--gold-primary);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--gold-primary);
            animation: pulse-gold 2s infinite;
        }

        @keyframes pulse-gold {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        /* Layout Container */
        .prod-layout-container {
            display: flex;
            flex: 1;
            height: calc(100vh - 64px);
            overflow: hidden;
        }

        /* Left Sidebar with Dynamic Submenu */
        .prod-sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            border-right: 1px solid #21262d;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            overflow-y: auto;
        }

        .sidebar-section-header {
            padding: 1.25rem 1rem 0.5rem 1rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }

        .sidebar-menu {
            padding: 0 0.75rem;
            list-style: none;
            margin: 0;
        }

        .sidebar-item {
            margin-bottom: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: #21262d;
            font-weight: 600;
            border-left: 3px solid var(--gold-primary);
        }

        .sidebar-link i {
            font-size: 1.1rem;
            color: #64748b;
        }

        .sidebar-link.active i,
        .sidebar-link:hover i {
            color: var(--gold-primary);
        }

        .sidebar-link .badge {
            margin-left: auto;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Main Workspace */
        .prod-content {
            flex: 1;
            overflow-y: auto;
            background-color: var(--page-bg);
        }
    </style>

    @livewireStyles
    @stack('styles')
</head>
<body>

    @php
        $currentRoute = request()->route() ? request()->route()->getName() : '';
        
        // Determine active top menu tab
        $activeTopSection = 'products';
        if (str_contains($currentRoute, 'expenses')) {
            $activeTopSection = 'expenses';
        } elseif (str_contains($currentRoute, 'vault') || str_contains($currentRoute, 'materials')) {
            $activeTopSection = 'vault';
        }
    @endphp

    <!-- Top Luxury Navigation Bar -->
    <header class="prod-topbar">
        <!-- Logo & Brand -->
        <a href="{{ route('production.products') }}" class="prod-brand">
            <img src="{{ asset('images/five-fingers-logo.png') }}" alt="Five Fingers Logo">
            <div class="prod-brand-text">
                <span class="prod-brand-title">FIVE FINGERS</span>
                <span class="prod-brand-badge">JEWELRY MANUFACTURING ERP</span>
            </div>
        </a>

        <!-- Top Menu: Products, Expenses, Purchasing/Vault -->
        <nav class="prod-top-nav">
            <a href="{{ route('production.products') }}" 
               class="prod-top-tab {{ $activeTopSection === 'products' ? 'active' : '' }}">
                <i class="bi bi-gem"></i>
                <span>Products & Jobs</span>
            </a>

            <a href="{{ route('production.expenses') }}" 
               class="prod-top-tab {{ $activeTopSection === 'expenses' ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Expenses & Costing</span>
            </a>

            <a href="{{ route('production.vault') }}" 
               class="prod-top-tab {{ $activeTopSection === 'vault' ? 'active' : '' }}">
                <i class="bi bi-safe2"></i>
                <span>Purchasing & Vault</span>
            </a>
        </nav>

        <!-- Right Side: Live Gold Ticker, Switch to Inventory & User -->
        <div class="d-flex align-items-center gap-3">
            <div class="gold-ticker d-none d-xl-flex">
                <span class="gold-dot"></span>
                <span><strong>24K Gold:</strong> Rs. 24,500/g</span>
                <span class="text-secondary">|</span>
                <span><strong>18K:</strong> Rs. 18,375/g</span>
            </div>

            <!-- Switch to Inventory ERP -->
            <a href="{{ route('admin.sales-list') }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2 text-white border-secondary">
                <i class="bi bi-arrow-left-right text-warning"></i>
                <span class="d-none d-md-inline">Inventory ERP</span>
            </a>

            <!-- User Menu -->
            <div class="dropdown">
                <button class="btn btn-dark btn-sm dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent text-white" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.75rem;">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <span class="d-none d-sm-inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow">
                    <li><h6 class="dropdown-header">{{ auth()->user()->email ?? '' }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i> Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main App Body: Left Sidebar + Dynamic Content -->
    <div class="prod-layout-container">
        
        <!-- Left Sidebar: Sub-menus dynamically adapt to active top menu -->
        <aside class="prod-sidebar">

            @if($activeTopSection === 'products')
                <!-- SUBMENU: PRODUCTS & MANUFACTURING JOBS -->
                <div class="sidebar-section-header">Manufacturing Jobs</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <a href="{{ route('production.products') }}" class="sidebar-link {{ request()->routeIs('production.products') && !request()->has('status') ? 'active' : '' }}">
                            <i class="bi bi-grid-fill"></i>
                            <span>All Jewelry Jobs</span>
                            <span class="badge bg-secondary">{{ \App\Models\ProductionProduct::count() }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['status' => 'in_production']) }}" class="sidebar-link {{ request()->get('status') === 'in_production' ? 'active' : '' }}">
                            <i class="bi bi-clock-history text-warning"></i>
                            <span>In Production</span>
                            <span class="badge bg-warning text-dark">{{ \App\Models\ProductionProduct::where('status', 'in_production')->count() }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['status' => 'qc_review']) }}" class="sidebar-link {{ request()->get('status') === 'qc_review' ? 'active' : '' }}">
                            <i class="bi bi-shield-check text-info"></i>
                            <span>QC & Inspection</span>
                            <span class="badge bg-info text-dark">{{ \App\Models\ProductionProduct::where('status', 'qc_review')->count() }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['status' => 'completed']) }}" class="sidebar-link {{ request()->get('status') === 'completed' ? 'active' : '' }}">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Completed & Vault</span>
                            <span class="badge bg-success">{{ \App\Models\ProductionProduct::where('status', 'completed')->count() }}</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section-header mt-3">Pipeline Stages</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['stage' => 1]) }}" class="sidebar-link">
                            <i class="bi bi-pencil-ruler"></i>
                            <span>1. 3D CAD Design</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['stage' => 8]) }}" class="sidebar-link">
                            <i class="bi bi-fire text-danger"></i>
                            <span>8. Metal Casting</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['stage' => 11]) }}" class="sidebar-link">
                            <i class="bi bi-tools"></i>
                            <span>11. Bench Filing</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['stage' => 12]) }}" class="sidebar-link">
                            <i class="bi bi-gem text-primary"></i>
                            <span>12. Stone Setting</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.products', ['stage' => 13]) }}" class="sidebar-link">
                            <i class="bi bi-stars text-warning"></i>
                            <span>13. Polishing</span>
                        </a>
                    </li>
                </ul>

            @elseif($activeTopSection === 'expenses')
                <!-- SUBMENU: EXPENSES & COSTING -->
                <div class="sidebar-section-header">Financial Audits</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <a href="{{ route('production.expenses') }}" class="sidebar-link {{ request()->routeIs('production.expenses') ? 'active' : '' }}">
                            <i class="bi bi-cash-stack"></i>
                            <span>All Production Costs</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.expenses', ['type' => 'labor']) }}" class="sidebar-link {{ request()->get('type') === 'labor' ? 'active' : '' }}">
                            <i class="bi bi-person-badge"></i>
                            <span>Artisan Labor Payouts</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.expenses', ['type' => 'metal_loss']) }}" class="sidebar-link {{ request()->get('type') === 'metal_loss' ? 'active' : '' }}">
                            <i class="bi bi-reception-3 text-warning"></i>
                            <span>Metal Loss Reconciliation</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.expenses', ['type' => 'consumable']) }}" class="sidebar-link {{ request()->get('type') === 'consumable' ? 'active' : '' }}">
                            <i class="bi bi-droplet"></i>
                            <span>Consumables & Gas</span>
                        </a>
                    </li>
                </ul>

            @elseif($activeTopSection === 'vault')
                <!-- SUBMENU: PURCHASING & VAULT STOCK -->
                <div class="sidebar-section-header">Raw Materials Vault</div>
                <ul class="sidebar-menu">
                    <li class="sidebar-item">
                        <a href="{{ route('production.vault') }}" class="sidebar-link {{ request()->routeIs('production.vault') ? 'active' : '' }}">
                            <i class="bi bi-safe2"></i>
                            <span>Vault Balance Overview</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.vault', ['tab' => 'bullion']) }}" class="sidebar-link {{ request()->get('tab') === 'bullion' ? 'active' : '' }}">
                            <i class="bi bi-circle-fill text-warning"></i>
                            <span>Gold & Silver Bullion</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.vault', ['tab' => 'stones']) }}" class="sidebar-link {{ request()->get('tab') === 'stones' ? 'active' : '' }}">
                            <i class="bi bi-gem text-info"></i>
                            <span>Loose Gemstones Stock</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('production.vault', ['tab' => 'consumables']) }}" class="sidebar-link {{ request()->get('tab') === 'consumables' ? 'active' : '' }}">
                            <i class="bi bi-box-seam"></i>
                            <span>Plaster, Wax & Supplies</span>
                        </a>
                    </li>
                </ul>
            @endif

            <!-- Quick Stats in Sidebar Footer -->
            <div class="mt-auto p-3 border-top border-secondary-subtle">
                <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.75rem;">
                    <span>System Status</span>
                    <span class="text-success"><i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i> Online</span>
                </div>
                <div class="mt-2 text-secondary" style="font-size: 0.7rem;">
                    Five Fingers Production v2.0
                </div>
            </div>

        </aside>

        <!-- Main Content Area -->
        <main class="prod-content">
            {{ $slot }}
        </main>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
