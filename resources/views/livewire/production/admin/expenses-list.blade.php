@php
    $rs = fn ($v, $d = 0) => number_format((float) $v, $d);
    $typeChip = [
        'labor' => ['violet', 'Artisan labour'],
        'consumable' => ['green', 'Consumables'],
        'energy' => ['amber', 'Kiln energy'],
        'plating' => ['blue', 'Rhodium plating'],
        'metal_loss' => ['red', 'Metal loss'],
    ];
@endphp

<div class="page">
    <div class="lw-loading" wire:loading.delay></div>

    <!-- Header -->
    <header class="page-head">
        <div>
            <div class="eyebrow"><i class="bi bi-wallet2"></i> Production accounting</div>
            <h1 class="page-title">Expenses & Costing</h1>
            <p class="page-sub">Direct stage expenditures, artisan piece-rate wages, and metal loss write-offs.</p>
        </div>
    </header>

    <!-- KPIs -->
    <section class="kpis" aria-label="Cost summary">
        <article class="card kpi" style="--kpi-c: var(--violet); --kpi-glow: rgba(196,181,253,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Artisan Labour</span>
                <span class="kpi-icon"><i class="bi bi-person-badge"></i></span>
            </div>
            <div class="kpi-value"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalLabor) }}</div>
            <div class="kpi-foot">Direct wages paid across stages</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--red); --kpi-glow: rgba(251,113,133,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Metal Loss Scrap</span>
                <span class="kpi-icon"><i class="bi bi-graph-down-arrow"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--red)"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalMetalLoss) }}</div>
            <div class="kpi-foot">Casting & polishing loss value</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--green); --kpi-glow: rgba(94,230,154,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Consumables & Gas</span>
                <span class="kpi-icon"><i class="bi bi-droplet"></i></span>
            </div>
            <div class="kpi-value"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalConsumables) }}</div>
            <div class="kpi-foot">Wax, investment powder, chemicals</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--gold); --kpi-glow: rgba(217,180,90,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Total Manufacturing</span>
                <span class="kpi-icon"><i class="bi bi-cash-stack"></i></span>
            </div>
            <div class="kpi-value gold-text"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($grandTotal) }}</div>
            <div class="kpi-foot">Combined workshop spend</div>
        </article>
    </section>

    <!-- Expenses Table -->
    <section class="card">
        <div class="toolbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" class="input" placeholder="Search description, job SKU or piece name…" wire:model.live.debounce.300ms="search" aria-label="Search expenses">
            </div>

            <div class="filter-tabs" role="tablist" aria-label="Expense type filter">
                @foreach (['' => 'All', 'labor' => 'Labour', 'consumable' => 'Consumables', 'energy' => 'Energy', 'plating' => 'Plating'] as $val => $label)
                    <button type="button" role="tab" class="{{ $expenseType === $val ? 'is-active' : '' }}" wire:click="$set('expenseType', '{{ $val }}')" aria-selected="{{ $expenseType === $val ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div class="table-wrap" tabindex="0" role="region" aria-labelledby="expenses-caption">
            <table class="tbl">
                <caption id="expenses-caption" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Manufacturing expenses</caption>
                <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Job / Piece</th>
                        <th scope="col">Stage</th>
                        <th scope="col">Category</th>
                        <th scope="col">Description</th>
                        <th scope="col" class="r">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $exp)
                        @php
                            [$chipColor, $chipLabel] = $typeChip[$exp->expense_type] ?? ['', ucfirst($exp->expense_type)];
                        @endphp
                        <tr>
                            <td class="dim tnum" style="white-space:nowrap">{{ $exp->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('production.lifecycle', $exp->production_product_id) }}" class="job-title" style="display:block">
                                    {{ $exp->product->title ?? 'N/A' }}
                                </a>
                                <span class="mono dim" style="font-size:11.5px">{{ $exp->product->sku ?? '' }}</span>
                            </td>
                            <td>
                                <span class="chip" style="height:22px;font-size:11px">
                                    {{ $exp->stage->step_number ?? '' }}. {{ $exp->stage->step_name ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <span class="chip {{ $chipColor }}" style="height:22px;font-size:11px">
                                    <span class="dot"></span>{{ $chipLabel }}
                                </span>
                            </td>
                            <td class="muted" style="font-size:13px">{{ $exp->description }}</td>
                            <td class="r">
                                <span class="money tnum"><span class="cur">Rs</span>{{ $rs($exp->amount) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty">
                                    <div class="empty-icon"><i class="bi bi-receipt"></i></div>
                                    <h3>No expenses logged yet</h3>
                                    <p class="dim">When artisans complete stages, their labor and consumables appear here automatically.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $expenses->links('livewire.production.partials.pager') }}
    </section>

</div>
