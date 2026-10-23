<div class="p-4">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-safe2 text-warning"></i>
                Purchasing & Raw Materials Vault
            </h2>
            <p class="text-muted small mb-0">Precious gold bullion balance, certified loose gemstones inventory, and ready-to-sell vaulted pieces.</p>
        </div>
    </div>

    <!-- Vault Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Gold Bullion In Vault</div>
                <div class="fs-4 fw-bold text-warning mt-1">250.000 g</div>
                <div class="small text-secondary mt-1">24K Pure Gold + 18K Master Alloys</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Metal Issued To Production</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($activeProducts->sum('target_metal_weight'), 2) }} g</div>
                <div class="small text-secondary mt-1">Across {{ $activeProducts->count() }} active jobs</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Allocated Gemstones</div>
                <div class="fs-4 fw-bold text-info mt-1">{{ $allocatedStones->sum('carat_weight') }} ctw</div>
                <div class="small text-secondary mt-1">{{ $allocatedStones->count() }} lots assigned to jobs</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Completed Vault Stock</div>
                <div class="fs-4 fw-bold text-success mt-1">{{ $completedProducts->count() }} Pieces</div>
                <div class="small text-secondary mt-1">Ready for transfer to retail showroom</div>
            </div>
        </div>
    </div>

    <!-- Vault Inventory Tabs -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom p-3">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'bullion' ? 'active bg-warning text-dark fw-bold' : 'text-secondary' }}" href="{{ route('production.vault', ['tab' => 'bullion']) }}">
                        <i class="bi bi-circle-fill text-warning me-1"></i> Precious Metal Bullion
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'stones' ? 'active bg-info text-dark fw-bold' : 'text-secondary' }}" href="{{ route('production.vault', ['tab' => 'stones']) }}">
                        <i class="bi bi-gem text-info me-1"></i> Loose Gemstones Vault
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'finished' ? 'active bg-success text-white fw-bold' : 'text-secondary' }}" href="{{ route('production.vault', ['tab' => 'finished']) }}">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Vault Ready Finished Jewelry
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            @if($activeTab === 'bullion')
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Alloy / Bullion Type</th>
                                <th class="py-3">Purity Standard</th>
                                <th class="py-3">Current Vault Balance</th>
                                <th class="py-3">Current Spot Rate / g</th>
                                <th class="py-3 text-end pe-3">Estimated Vault Asset Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <i class="bi bi-circle-fill text-warning me-2"></i> 24K Pure Gold (Au 999.9)
                                </td>
                                <td>99.99% Fine Gold</td>
                                <td class="fw-bold font-monospace">150.000 g</td>
                                <td>Rs. 24,500.00</td>
                                <td class="text-end pe-3 fw-bold font-monospace text-success">Rs. 3,675,000.00</td>
                            </tr>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <i class="bi bi-circle-fill text-warning me-2"></i> 22K Sovereign Gold (Au 916)
                                </td>
                                <td>91.60% Fine Gold</td>
                                <td class="fw-bold font-monospace">100.000 g</td>
                                <td>Rs. 22,460.00</td>
                                <td class="text-end pe-3 fw-bold font-monospace text-success">Rs. 2,246,000.00</td>
                            </tr>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <i class="bi bi-circle-fill text-secondary me-2"></i> 950 Casting Platinum
                                </td>
                                <td>95.00% Pure Pt</td>
                                <td class="fw-bold font-monospace">85.000 g</td>
                                <td>Rs. 12,800.00</td>
                                <td class="text-end pe-3 fw-bold font-monospace text-success">Rs. 1,088,000.00</td>
                            </tr>
                            <tr>
                                <td class="ps-3 fw-bold text-dark">
                                    <i class="bi bi-circle-fill text-info me-2"></i> 925 Sterling Silver Grain
                                </td>
                                <td>92.50% Ag Fine</td>
                                <td class="fw-bold font-monospace">500.000 g</td>
                                <td>Rs. 320.00</td>
                                <td class="text-end pe-3 fw-bold font-monospace text-success">Rs. 160,000.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            @elseif($activeTab === 'stones')
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">Assigned Job / Piece</th>
                                <th class="py-3">Gemstone Type</th>
                                <th class="py-3">Cut & Shape</th>
                                <th class="py-3">Quantity</th>
                                <th class="py-3">Carat Weight</th>
                                <th class="py-3">Setting Method</th>
                                <th class="py-3 text-end pe-3">Unit Valuation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allocatedStones as $st)
                                <tr>
                                    <td class="ps-3">
                                        <a href="{{ route('production.lifecycle', $st->production_product_id) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $st->product->title ?? 'N/A' }}
                                        </a>
                                        <div class="small text-muted font-monospace">{{ $st->product->sku ?? '' }}</div>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-gem text-info me-1"></i> {{ $st->stone_type }}
                                    </td>
                                    <td>{{ $st->cut_shape }} ({{ $st->dimensions_mm ?: 'Standard' }})</td>
                                    <td>{{ $st->quantity }}</td>
                                    <td class="fw-bold">{{ $st->carat_weight }} ct</td>
                                    <td><span class="badge bg-light text-dark border">{{ $st->setting_method }}</span></td>
                                    <td class="text-end pe-3 fw-bold font-monospace">
                                        Rs. {{ number_format($st->stone_unit_cost, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No stones allocated to jobs yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-3 py-3">SKU / Job</th>
                                <th class="py-3">Finished Jewelry Piece</th>
                                <th class="py-3">Finished Weight</th>
                                <th class="py-3 text-end">Total Manufacturing Cost</th>
                                <th class="py-3 text-end">Recommended Selling Price</th>
                                <th class="pe-3 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedProducts as $cp)
                                <tr>
                                    <td class="ps-3 font-monospace fw-bold">{{ $cp->sku }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $cp->title }}</div>
                                        <div class="small text-muted">{{ $cp->category }} | {{ $cp->metal_type }}</div>
                                    </td>
                                    <td class="fw-bold">{{ number_format($cp->target_metal_weight, 3) }} g</td>
                                    <td class="text-end font-monospace fw-bold text-dark">
                                        Rs. {{ number_format($cp->total_manufacturing_cost, 2) }}
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-success">
                                        Rs. {{ number_format($cp->recommended_selling_price, 2) }}
                                    </td>
                                    <td class="text-center pe-3">
                                        <a href="{{ route('production.lifecycle', $cp->id) }}" class="btn btn-sm btn-outline-dark">
                                            View Audit
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No completed pieces in vault stock yet. Advance all stages to complete a job.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>
