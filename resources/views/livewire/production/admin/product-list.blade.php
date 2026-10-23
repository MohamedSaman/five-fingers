@php
    $catIcons = ['Ring' => 'bi-record-circle', 'Necklace' => 'bi-suit-heart', 'Pendant' => 'bi-droplet', 'Earrings' => 'bi-moon-stars', 'Bracelet' => 'bi-circle', 'Bangle' => 'bi-circle'];
    $metalTone = fn ($m) => str_contains($m, 'White') || str_contains($m, 'Platinum') || str_contains($m, 'Silver') ? 'white' : (str_contains($m, 'Rose') ? 'rose' : '');
    $statusChip = ['in_production' => ['amber', 'In production'], 'qc_review' => ['red', 'QC review'], 'completed' => ['green', 'Completed'], 'draft' => ['', 'Draft']];
    $rs = fn ($v, $d = 0) => number_format((float) $v, $d);
@endphp

<div class="page">
    <div class="lw-loading" wire:loading.delay></div>

    <!-- Header -->
    <header class="page-head">
        <div>
            <div class="eyebrow"><i class="bi bi-gem"></i> Atelier workshop</div>
            <h1 class="page-title">Jewellery Jobs</h1>
            <p class="page-sub">Every piece from CAD to vault — stage by stage, gram by gram, rupee by rupee.</p>
        </div>
        <button type="button" class="btn btn-gold btn-lg" wire:click="openModal" id="btn-create-job">
            <i class="bi bi-plus-lg"></i> New jewellery job
        </button>
    </header>

    <!-- KPIs -->
    <section class="kpis" aria-label="Summary">
        <article class="card kpi">
            <div class="kpi-top"><span class="kpi-label">On the bench</span><span class="kpi-icon"><i class="bi bi-hammer"></i></span></div>
            <div class="kpi-value">{{ $kpi['active'] }}<small>pieces</small></div>
            <div class="kpi-foot">{{ $kpi['done'] }} completed overall</div>
        </article>
        <article class="card kpi" style="--kpi-c: var(--amber); --kpi-glow: rgba(251,191,36,.14)">
            <div class="kpi-top"><span class="kpi-label">Metal in process</span><span class="kpi-icon"><i class="bi bi-coin"></i></span></div>
            <div class="kpi-value">{{ number_format($kpi['metal'], 2) }}<small>g</small></div>
            <div class="kpi-foot">Issued to active jobs</div>
        </article>
        <article class="card kpi" style="--kpi-c: var(--blue); --kpi-glow: rgba(125,211,252,.12)">
            <div class="kpi-top"><span class="kpi-label">WIP cost</span><span class="kpi-icon"><i class="bi bi-cash-stack"></i></span></div>
            <div class="kpi-value"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($kpi['wip']) }}</div>
            <div class="kpi-foot">Accrued across active jobs</div>
        </article>
        <article class="card kpi" style="--kpi-c: var(--green); --kpi-glow: rgba(94,230,154,.12)">
            <div class="kpi-top"><span class="kpi-label">Pipeline value</span><span class="kpi-icon"><i class="bi bi-graph-up-arrow"></i></span></div>
            <div class="kpi-value"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($kpi['value']) }}</div>
            <div class="kpi-foot">{{ $kpi['qc'] }} awaiting QC</div>
        </article>
    </section>

    <!-- Jobs table -->
    <section class="card">
        <div class="toolbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" class="input" placeholder="Search by title, SKU or category…" wire:model.live.debounce.300ms="search" aria-label="Search jobs">
            </div>

            <div class="filter-tabs" role="tablist" aria-label="Status filter">
                @foreach (['' => 'All', 'in_production' => 'Active', 'qc_review' => 'QC', 'completed' => 'Done'] as $val => $label)
                    <button type="button" role="tab" class="{{ $statusFilter === $val ? 'is-active' : '' }}" wire:click="setStatus('{{ $val }}')" aria-selected="{{ $statusFilter === $val ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </div>

            <select class="select" wire:model.live="metalFilter" aria-label="Metal filter">
                <option value="">All metals</option>
                @foreach (['18K Yellow Gold', '18K White Gold', '18K Rose Gold', '22K Yellow Gold', '24K Pure Gold', '950 Platinum', '925 Sterling Silver'] as $m)
                    <option value="{{ $m }}">{{ $m }}</option>
                @endforeach
            </select>

            @if ($stageFilter)
                <span class="chip gold">Stage {{ $stageFilter }} · {{ $standardStages[$stageFilter]['name'] ?? '' }}
                    <button type="button" wire:click="$set('stageFilter','')" style="all:unset;cursor:pointer;margin-left:4px" aria-label="Clear stage filter"><i class="bi bi-x"></i></button>
                </span>
            @endif
        </div>

        <div class="table-wrap" tabindex="0" role="region" aria-labelledby="jobs-caption">
            <table class="tbl">
                <caption id="jobs-caption" style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Jewellery manufacturing jobs</caption>
                <thead>
                    <tr>
                        <th scope="col">Piece</th>
                        <th scope="col">Gemstones</th>
                        <th scope="col">Lifecycle</th>
                        <th scope="col" class="r">Cost so far</th>
                        <th scope="col" class="r">Retail (MSRP)</th>
                        <th scope="col"><span style="position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0)">Open</span></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        @php
                            $total = $product->stages->count();
                            $done = $product->stages->where('status', 'completed')->count();
                            $pct = $total ? round($done / $total * 100) : 0;
                            $current = $product->stages->firstWhere('status', 'in_progress') ?? $product->stages->firstWhere('status', 'failed');
                            [$sc, $sl] = $statusChip[$product->status] ?? ['', ucfirst($product->status)];
                        @endphp
                        <tr class="row-link" wire:key="job-{{ $product->id }}" onclick="window.location='{{ route('production.lifecycle', $product->id) }}'">
                            <th scope="row">
                                <div class="job">
                                    <div class="job-thumb {{ $metalTone($product->metal_type) }}"><i class="bi {{ $catIcons[$product->category] ?? 'bi-gem' }}"></i></div>
                                    <div style="min-width:0">
                                        <div class="job-title">{{ $product->title }}</div>
                                        <div class="job-meta">
                                            <span class="mono">{{ $product->sku }}</span>
                                            <span>·</span>
                                            <span>{{ $product->metal_type }}</span>
                                            <span>·</span>
                                            <span class="tnum">{{ number_format($product->target_metal_weight, 2) }} g</span>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <td data-label="Gemstones">
                                @forelse ($product->stones->take(2) as $stone)
                                    <div class="stone-line"><i class="bi bi-diamond-fill"></i> {{ $stone->quantity }}× {{ $stone->stone_type }} <span class="dim tnum">{{ rtrim(rtrim(number_format($stone->carat_weight, 3), '0'), '.') }}ct</span></div>
                                @empty
                                    <span class="dim" style="font-size:12.5px">Plain metal</span>
                                @endforelse
                                @if ($product->stones->count() > 2)
                                    <span class="dim" style="font-size:11.5px">+{{ $product->stones->count() - 2 }} more</span>
                                @endif
                            </td>
                            <td data-label="Lifecycle" class="progress-cell">
                                <div class="progress-top">
                                    <span style="display:flex;align-items:center;gap:8px;min-width:0">
                                        <span class="chip {{ $sc }}" style="height:20px;font-size:10.5px"><span class="dot"></span>{{ $sl }}</span>
                                        <span style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $current?->step_name ?? ($product->status === 'completed' ? 'In vault' : '—') }}</span>
                                    </span>
                                    <span class="dim tnum" style="white-space:nowrap">{{ $done }}/{{ $total }}</span>
                                </div>
                                <div class="progress"><span style="width: {{ $pct }}%"></span></div>
                            </td>
                            <td data-label="Cost so far" class="r">
                                <div class="money"><span class="cur">Rs</span>{{ $rs($product->total_manufacturing_cost) }}</div>
                                <div class="sub-money">Loss Rs {{ $rs($product->total_metal_loss_cost) }}</div>
                            </td>
                            <td data-label="Retail (MSRP)" class="r">
                                <div class="money green"><span class="cur">Rs</span>{{ $rs($product->recommended_selling_price) }}</div>
                                <div class="sub-money">+{{ rtrim(rtrim(number_format($product->markup_percentage, 2), '0'), '.') }}% markup</div>
                            </td>
                            <td class="r">
                                <a href="{{ route('production.lifecycle', $product->id) }}" class="btn btn-sm" onclick="event.stopPropagation()">
                                    Lifecycle <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty">
                                    <div class="empty-icon"><i class="bi bi-gem"></i></div>
                                    <h3>No jobs here yet</h3>
                                    <p>Start a new piece — choose metal, stones and the exact stages it needs.</p>
                                    <button type="button" class="btn btn-gold" wire:click="openModal"><i class="bi bi-plus-lg"></i> New jewellery job</button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links('livewire.production.partials.pager') }}
    </section>

    <!-- ================= CREATE JOB DIALOG ================= -->
    @if ($showCreateModal)
        <dialog class="pd-dialog" wire:ignore.self x-data x-init="$el.showModal()" @close="$wire.closeModal()" aria-labelledby="create-job-title">
            <form wire:submit="createProduct" style="display:contents">
                <div class="dlg-head">
                    <div>
                        <div class="eyebrow" style="margin-bottom:4px"><i class="bi bi-stars"></i> New piece</div>
                        <h2 class="dlg-title" id="create-job-title">Create jewellery job</h2>
                    </div>
                    <button type="button" class="icon-btn" wire:click="closeModal" aria-label="Close"><i class="bi bi-x-lg"></i></button>
                </div>

                <div class="dlg-body">

                    <!-- 1. Design & metal -->
                    <section class="dlg-section">
                        <div class="dlg-section-head">
                            <div class="dlg-section-title">
                                <span class="step-num">1</span>
                                <div><h3>Design & metal</h3><p>What are we making, and in which alloy?</p></div>
                            </div>
                        </div>

                        <div class="grid-4" style="grid-template-columns: 1fr 2fr 1fr 1.2fr">
                            <div class="field">
                                <label for="f-sku">Job SKU</label>
                                <input id="f-sku" class="input mono" wire:model="sku" required>
                                @error('sku') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="field">
                                <label for="f-title">Piece title</label>
                                <input id="f-title" class="input" wire:model="title" placeholder="e.g. Royal Blue Sapphire Halo Ring" required autofocus>
                                @error('title') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="field">
                                <label for="f-cat">Category</label>
                                <select id="f-cat" class="select" wire:model="category">
                                    @foreach (['Ring', 'Necklace', 'Pendant', 'Earrings', 'Bracelet', 'Bangle', 'Custom'] as $c)
                                        <option>{{ $c }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="f-metal">Metal alloy</label>
                                <select id="f-metal" class="select" wire:model.live="metal_type">
                                    <option value="18K Yellow Gold">18K Yellow Gold · 750</option>
                                    <option value="18K White Gold">18K White Gold · 750</option>
                                    <option value="18K Rose Gold">18K Rose Gold · 750</option>
                                    <option value="22K Yellow Gold">22K Yellow Gold · 916</option>
                                    <option value="24K Pure Gold">24K Pure Gold · 999</option>
                                    <option value="950 Platinum">950 Platinum</option>
                                    <option value="925 Sterling Silver">925 Sterling Silver</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid-3" style="margin-top:12px">
                            <div class="field">
                                <label for="f-weight">Target metal weight</label>
                                <div class="input-wrap"><input id="f-weight" type="number" step="0.001" min="0" class="input" wire:model.live.debounce.400ms="target_metal_weight" required><span class="unit">g</span></div>
                                @error('target_metal_weight') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="field">
                                <label for="f-rate">Gold rate</label>
                                <div class="input-wrap prefix"><span class="unit">Rs</span><input id="f-rate" type="number" step="1" min="0" class="input" wire:model.live.debounce.400ms="metal_unit_price_per_g" required></div>
                            </div>
                            <div class="field">
                                <label for="f-markup">Markup</label>
                                <div class="input-wrap"><input id="f-markup" type="number" step="1" min="0" class="input" wire:model.live.debounce.400ms="markup_percentage"><span class="unit">%</span></div>
                            </div>
                        </div>

                        <label class="mold-toggle">
                            <input type="checkbox" class="switch" wire:model.live="is_master_piece_required">
                            <div style="flex:1">
                                <strong>Master piece & rubber mold</strong>
                                <span>Turn on for repeat production — adds 3D master print, vulcanised mold and wax injection.</span>
                            </div>
                            <span class="chip {{ $is_master_piece_required ? 'gold' : '' }}">{{ $is_master_piece_required ? 'Repeatable design' : 'One-off piece' }}</span>
                        </label>
                    </section>

                    <!-- 2. Gemstones -->
                    <section class="dlg-section">
                        <div class="dlg-section-head">
                            <div class="dlg-section-title">
                                <span class="step-num">2</span>
                                <div><h3>Gemstones</h3><p>Stone type, size, quantity and how it will be set.</p></div>
                            </div>
                            <button type="button" class="btn btn-sm" wire:click="addStoneRow"><i class="bi bi-plus-lg"></i> Add stone</button>
                        </div>

                        <div class="stone-rows">
                            @forelse ($stones as $i => $stone)
                                <div class="stone-row" wire:key="stone-row-{{ $i }}">
                                    <div class="field"><label>Stone</label><input class="input input-sm" wire:model.blur="stones.{{ $i }}.stone_type" placeholder="Blue Sapphire"></div>
                                    <div class="field"><label>Cut</label>
                                        <select class="select select-sm" wire:model="stones.{{ $i }}.cut_shape">
                                            @foreach (['Round Brilliant', 'Oval', 'Cushion', 'Princess', 'Emerald Cut', 'Pear', 'Marquise', 'Cabochon'] as $cut)
                                                <option>{{ $cut }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field"><label>Size (mm)</label><input class="input input-sm" wire:model="stones.{{ $i }}.dimensions_mm" placeholder="7x5"></div>
                                    <div class="field"><label>Qty</label><input type="number" min="1" class="input input-sm" wire:model.live.debounce.400ms="stones.{{ $i }}.quantity"></div>
                                    <div class="field"><label>Carat</label><input type="number" step="0.001" min="0" class="input input-sm" wire:model="stones.{{ $i }}.carat_weight"></div>
                                    <div class="field"><label>Setting</label>
                                        <select class="select select-sm" wire:model.live="stones.{{ $i }}.setting_method">
                                            <option value="hand_prong">Hand · Prong</option>
                                            <option value="bezel">Hand · Bezel</option>
                                            <option value="pave">Pavé</option>
                                            <option value="channel">Channel</option>
                                            <option value="wax_cast">Wax set (cast-in-place)</option>
                                        </select>
                                    </div>
                                    <div class="field"><label>Unit cost (Rs)</label><input type="number" min="0" class="input input-sm" wire:model.live.debounce.400ms="stones.{{ $i }}.stone_unit_cost"></div>
                                    <button type="button" class="icon-btn danger" wire:click="removeStoneRow({{ $i }})" aria-label="Remove stone"><i class="bi bi-trash3"></i></button>
                                </div>
                            @empty
                                <div class="notice"><i class="bi bi-info-circle"></i> Plain metal piece — no stones. Use “Add stone” if needed.</div>
                            @endforelse
                        </div>
                    </section>

                    <!-- 3. Stages -->
                    <section class="dlg-section">
                        <div class="dlg-section-head">
                            <div class="dlg-section-title">
                                <span class="step-num">3</span>
                                <div><h3>Production stages</h3><p>Only the stages you keep selected will appear in this piece's lifecycle.</p></div>
                            </div>
                            <span class="chip gold tnum">{{ count(array_filter($selectedStages)) }} / 15 stages</span>
                        </div>
                        @error('selectedStages') <div class="notice red" style="margin-bottom:10px"><i class="bi bi-exclamation-triangle"></i> {{ $message }}</div> @enderror

                        <div class="stage-picker">
                            @foreach ($standardStages as $num => $meta)
                                <label class="stage-pick" wire:key="pick-{{ $num }}">
                                    <input type="checkbox" wire:model.live="selectedStages.{{ $num }}">
                                    <span class="sp-num">{{ $num }}</span>
                                    <span style="min-width:0;padding-right:20px">
                                        <span class="sp-name" style="display:block">{{ $meta['name'] }}</span>
                                        <span class="sp-desc">{{ $meta['description'] }}</span>
                                        <span class="sp-role"><i class="bi bi-person"></i>{{ $meta['worker_role'] }}</span>
                                    </span>
                                    <span class="sp-tick"><i class="bi bi-check-lg"></i></span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                </div>

                <div class="dlg-foot">
                    <div class="estimate">
                        <div class="estimate-item"><span class="label">Metal</span><span class="money"><span class="cur">Rs</span>{{ $rs($this->estimate['metal']) }}</span></div>
                        <div class="estimate-item"><span class="label">Stones</span><span class="money"><span class="cur">Rs</span>{{ $rs($this->estimate['stones']) }}</span></div>
                        <div class="estimate-item"><span class="label">Base cost</span><span class="money gold"><span class="cur">Rs</span>{{ $rs($this->estimate['base']) }}</span></div>
                        <div class="estimate-item"><span class="label">Est. MSRP</span><span class="money green"><span class="cur">Rs</span>{{ $rs($this->estimate['msrp']) }}</span></div>
                    </div>
                    <div style="display:flex;gap:8px">
                        <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-gold" wire:loading.attr="disabled" wire:target="createProduct">
                            <span wire:loading.remove wire:target="createProduct"><i class="bi bi-rocket-takeoff"></i> Create & start lifecycle</span>
                            <span wire:loading wire:target="createProduct">Creating…</span>
                        </button>
                    </div>
                </div>
            </form>
        </dialog>
    @endif
</div>
