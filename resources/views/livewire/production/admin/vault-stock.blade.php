@php
    $rs = fn ($v, $d = 0) => number_format((float) $v, $d);
@endphp

<div class="page">
    <div class="lw-loading" wire:loading.delay></div>

    <!-- Header -->
    <header class="page-head">
        <div>
            <div class="eyebrow"><i class="bi bi-safe2"></i> Treasury & Inventory</div>
            <h1 class="page-title">Purchasing & Vault</h1>
            <p class="page-sub">Precious metal bullion holdings, certified loose gemstones, and vault-ready completed pieces.</p>
        </div>
    </header>

    <!-- KPIs -->
    <section class="kpis" aria-label="Vault assets">
        <article class="card kpi" style="--kpi-c: var(--gold); --kpi-glow: rgba(217,180,90,.16)">
            <div class="kpi-top">
                <span class="kpi-label">Vault Bullion</span>
                <span class="kpi-icon"><i class="bi bi-coin"></i></span>
            </div>
            <div class="kpi-value gold-text">250.000<small>g</small></div>
            <div class="kpi-foot">24K Pure Gold + Master Alloys</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--amber); --kpi-glow: rgba(251,191,36,.14)">
            <div class="kpi-top">
                <span class="kpi-label">Metal in Production</span>
                <span class="kpi-icon"><i class="bi bi-fire"></i></span>
            </div>
            <div class="kpi-value">{{ number_format($activeProducts->sum('target_metal_weight'), 2) }}<small>g</small></div>
            <div class="kpi-foot">Across {{ $activeProducts->count() }} active jobs</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--blue); --kpi-glow: rgba(125,211,252,.14)">
            <div class="kpi-top">
                <span class="kpi-label">Allocated Gemstones</span>
                <span class="kpi-icon"><i class="bi bi-gem"></i></span>
            </div>
            <div class="kpi-value">{{ $allocatedStones->sum('carat_weight') }}<small>ctw</small></div>
            <div class="kpi-foot">{{ $allocatedStones->count() }} lots assigned</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--green); --kpi-glow: rgba(94,230,154,.14)">
            <div class="kpi-top">
                <span class="kpi-label">Vault Ready Finished</span>
                <span class="kpi-icon"><i class="bi bi-patch-check"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--green)">{{ $completedProducts->count() }}<small>pieces</small></div>
            <div class="kpi-foot">Ready for retail showroom transfer</div>
        </article>
    </section>

    <!-- Vault Inventory Card -->
    <section class="card">
        <div class="toolbar">
            <div class="filter-tabs" role="tablist" aria-label="Vault category tabs">
                <button type="button" role="tab" class="{{ $activeTab === 'bullion' ? 'is-active' : '' }}" wire:click="$set('activeTab', 'bullion')" aria-selected="{{ $activeTab === 'bullion' ? 'true' : 'false' }}">
                    <i class="bi bi-coin me-1"></i> Precious Bullion
                </button>
                <button type="button" role="tab" class="{{ $activeTab === 'stones' ? 'is-active' : '' }}" wire:click="$set('activeTab', 'stones')" aria-selected="{{ $activeTab === 'stones' ? 'true' : 'false' }}">
                    <i class="bi bi-gem me-1"></i> Gemstones Vault
                </button>
                <button type="button" role="tab" class="{{ $activeTab === 'finished' ? 'is-active' : '' }}" wire:click="$set('activeTab', 'finished')" aria-selected="{{ $activeTab === 'finished' ? 'true' : 'false' }}">
                    <i class="bi bi-patch-check me-1"></i> Finished Pieces
                </button>
            </div>
        </div>

        <div class="table-wrap" tabindex="0" role="region" aria-labelledby="vault-caption">
            @if ($activeTab === 'bullion')
                <table class="tbl">
                    <caption id="vault-caption" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Bullion inventory</caption>
                    <thead>
                        <tr>
                            <th scope="col">Bullion Alloy</th>
                            <th scope="col">Purity Standard</th>
                            <th scope="col">Vault Balance</th>
                            <th scope="col">Spot Rate / g</th>
                            <th scope="col" class="r">Asset Valuation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="chip gold" style="width:28px;height:28px;padding:0;display:grid;place-items:center"><i class="bi bi-coin"></i></span>
                                    <div>
                                        <div style="font-weight:700">24K Pure Gold</div>
                                        <span class="mono dim">Au 999.9</span>
                                    </div>
                                </div>
                            </th>
                            <td>99.99% Fine Gold</td>
                            <td class="money gold tnum">150.000 g</td>
                            <td class="tnum">Rs 24,500.00</td>
                            <td class="r money green tnum"><span class="cur">Rs</span>3,675,000.00</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="chip amber" style="width:28px;height:28px;padding:0;display:grid;place-items:center"><i class="bi bi-coin"></i></span>
                                    <div>
                                        <div style="font-weight:700">22K Sovereign Gold</div>
                                        <span class="mono dim">Au 916</span>
                                    </div>
                                </div>
                            </th>
                            <td>91.60% Fine Gold</td>
                            <td class="money tnum">100.000 g</td>
                            <td class="tnum">Rs 22,460.00</td>
                            <td class="r money green tnum"><span class="cur">Rs</span>2,246,000.00</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="chip" style="width:28px;height:28px;padding:0;display:grid;place-items:center"><i class="bi bi-coin"></i></span>
                                    <div>
                                        <div style="font-weight:700">950 Casting Platinum</div>
                                        <span class="mono dim">Pt 950</span>
                                    </div>
                                </div>
                            </th>
                            <td>95.00% Pure Pt</td>
                            <td class="money tnum">85.000 g</td>
                            <td class="tnum">Rs 12,800.00</td>
                            <td class="r money green tnum"><span class="cur">Rs</span>1,088,000.00</td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="chip blue" style="width:28px;height:28px;padding:0;display:grid;place-items:center"><i class="bi bi-coin"></i></span>
                                    <div>
                                        <div style="font-weight:700">925 Sterling Silver Grain</div>
                                        <span class="mono dim">Ag 925</span>
                                    </div>
                                </div>
                            </th>
                            <td>92.50% Ag Fine</td>
                            <td class="money tnum">500.000 g</td>
                            <td class="tnum">Rs 320.00</td>
                            <td class="r money green tnum"><span class="cur">Rs</span>160,000.00</td>
                        </tr>
                    </tbody>
                </table>
            @elseif ($activeTab === 'stones')
                <table class="tbl">
                    <caption id="vault-caption" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Gemstones inventory</caption>
                    <thead>
                        <tr>
                            <th scope="col">Assigned Job</th>
                            <th scope="col">Gemstone Variety</th>
                            <th scope="col">Cut & Shape</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Carat Weight</th>
                            <th scope="col">Setting Style</th>
                            <th scope="col" class="r">Valuation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allocatedStones as $st)
                            <tr>
                                <td>
                                    <a href="{{ route('production.lifecycle', $st->production_product_id) }}" class="job-title">
                                        {{ $st->product->title ?? 'N/A' }}
                                    </a>
                                    <span class="mono dim" style="font-size:11.5px">{{ $st->product->sku ?? '' }}</span>
                                </td>
                                <td>
                                    <span style="font-weight:700"><i class="bi bi-gem text-info me-1"></i> {{ $st->stone_type }}</span>
                                </td>
                                <td>{{ $st->cut_shape }} @if ($st->dimensions_mm) <span class="dim">({{ $st->dimensions_mm }})</span> @endif</td>
                                <td class="tnum">{{ $st->quantity }}</td>
                                <td class="money tnum">{{ $st->carat_weight }} ct</td>
                                <td><span class="chip">{{ $st->setting_method }}</span></td>
                                <td class="r money tnum"><span class="cur">Rs</span>{{ $rs($st->stone_unit_cost) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty">
                                        <div class="empty-icon"><i class="bi bi-gem"></i></div>
                                        <h3>No stones allocated</h3>
                                        <p class="dim">Stones added to jewellery jobs appear here with their carat weights and setting methods.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="tbl">
                    <caption id="vault-caption" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Vault ready pieces</caption>
                    <thead>
                        <tr>
                            <th scope="col">SKU / Job</th>
                            <th scope="col">Finished Jewellery Piece</th>
                            <th scope="col">Final Weight</th>
                            <th scope="col" class="r">Total Manufacturing Cost</th>
                            <th scope="col" class="r">MSRP Retail Price</th>
                            <th scope="col" class="r">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($completedProducts as $cp)
                            <tr>
                                <td><span class="chip mono-chip">{{ $cp->sku }}</span></td>
                                <td>
                                    <div class="job-title">{{ $cp->title }}</div>
                                    <span class="dim" style="font-size:12px">{{ $cp->category }} · {{ $cp->metal_type }}</span>
                                </td>
                                <td class="money tnum">{{ number_format($cp->target_metal_weight, 3) }} g</td>
                                <td class="r money tnum"><span class="cur">Rs</span>{{ $rs($cp->total_manufacturing_cost) }}</td>
                                <td class="r money green tnum"><span class="cur">Rs</span>{{ $rs($cp->recommended_selling_price) }}</td>
                                <td class="r">
                                    <a href="{{ route('production.lifecycle', $cp->id) }}" class="btn btn-sm">Audit & Specs</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty">
                                        <div class="empty-icon"><i class="bi bi-box2-heart"></i></div>
                                        <h3>No completed pieces yet</h3>
                                        <p class="dim">Complete Stage 15 (Final QC & Hallmarking) on an active job to vault it.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </section>

</div>
