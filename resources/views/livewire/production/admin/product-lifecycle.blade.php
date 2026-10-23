@php
    $catIcons = ['Ring' => 'bi-record-circle', 'Necklace' => 'bi-suit-heart', 'Pendant' => 'bi-droplet', 'Earrings' => 'bi-moon-stars', 'Bracelet' => 'bi-circle', 'Bangle' => 'bi-circle'];
    $metalTone = fn ($m) => str_contains($m, 'White') || str_contains($m, 'Platinum') || str_contains($m, 'Silver') ? 'white' : (str_contains($m, 'Rose') ? 'rose' : '');
    $statusChip = ['in_production' => ['amber', 'In production'], 'qc_review' => ['red', 'QC review'], 'completed' => ['green', 'Completed'], 'draft' => ['', 'Draft']];
    [$prodSc, $prodSl] = $statusChip[$product->status] ?? ['', ucfirst($product->status)];

    $stageDone = $selectedStage->status === 'completed';
    $stageActive = $selectedStage->status === 'in_progress';
    $stageFailed = $selectedStage->status === 'failed';

    $rs = fn ($v, $d = 0) => number_format((float) $v, $d);

    // Cost percentages for the breakdown bar
    $totalCost = max(1, (float) $product->total_manufacturing_cost);
    $pctMetal = round(((float) $product->total_metal_cost / $totalCost) * 100);
    $pctStone = round(((float) $product->total_stone_cost / $totalCost) * 100);
    $pctLabor = round(((float) $product->total_labor_cost / $totalCost) * 100);
    $pctCons = round(((float) $product->total_expenses_cost / $totalCost) * 100);
    $pctLoss = max(0, 100 - ($pctMetal + $pctStone + $pctLabor + $pctCons));
@endphp

<div class="lc">

    <div class="lw-loading" wire:loading.delay></div>

    <!-- ================= TOP HERO / SUMMARY BAR ================= -->
    <header class="lc-hero">
        <div class="lc-hero-left">
            <a href="{{ route('production.products') }}" class="btn btn-sm btn-ghost" title="Back to All Jobs" aria-label="Back to All Jobs">
                <i class="bi bi-arrow-left"></i>
            </a>

            @if ($product->image_url)
                <img src="{{ asset($product->image_url) }}" alt="{{ $product->title }}" class="hero-thumb-img">
            @else
                <div class="job-thumb {{ $metalTone($product->metal_type) }}" style="width:52px;height:52px;font-size:24px">
                    <i class="bi {{ $catIcons[$product->category] ?? 'bi-gem' }}"></i>
                </div>
            @endif

            <div style="min-width:0">
                <div class="lc-chips">
                    <span class="chip mono-chip">{{ $product->sku }}</span>
                    <span class="chip {{ $prodSc }}"><span class="dot"></span>{{ $prodSl }}</span>
                    <span class="chip">{{ $product->category }}</span>
                    @if ($product->is_master_piece_required)
                        <span class="chip gold"><i class="bi bi-box"></i> Master Mold</span>
                    @endif
                </div>

                <h1 class="lc-title">{{ $product->title }}</h1>

                <div class="lc-meta">
                    <span><i class="bi bi-coin"></i> {{ $product->metal_type }}</span>
                    <span><i class="bi bi-scale"></i> Target <b class="tnum">{{ number_format($product->target_metal_weight, 3) }} g</b></span>
                    <span><i class="bi bi-tag"></i> Gold base <b class="tnum">Rs {{ $rs($product->metal_unit_price_per_g) }}/g</b></span>
                    @if ($totalHours > 0)
                        <span><i class="bi bi-stopwatch"></i> <b class="tnum">{{ $totalHours }} hrs</b> logged</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Metrics -->
        <div class="lc-stats">
            <div class="lc-stat ring-stat">
                <div class="ring" style="--p: {{ $progress }}">{{ $progress }}%</div>
                <div>
                    <span class="label">Progress</span>
                    <div class="val tnum">{{ $doneCount }}/{{ $product->stages->count() }} <small class="dim">done</small></div>
                </div>
            </div>

            <div class="lc-stat">
                <span class="label">Total Cost</span>
                <div class="money gold"><span class="cur">Rs</span>{{ $rs($product->total_manufacturing_cost) }}</div>
                <div class="dim" style="font-size:11px;margin-top:2px">Metal, gems, labor, loss</div>
            </div>

            <div class="lc-stat">
                <span class="label">Retail (MSRP)</span>
                <div class="money green"><span class="cur">Rs</span>{{ $rs($product->recommended_selling_price) }}</div>
                <div class="dim" style="font-size:11px;margin-top:2px">+{{ rtrim(rtrim(number_format($product->markup_percentage, 2), '0'), '.') }}% markup</div>
            </div>
        </div>
    </header>

    <!-- ================= 3-COLUMN WORKSPACE ================= -->
    <div class="lc-grid">

        <!-- ============================================== -->
        <!-- COLUMN 1: TOP-TO-BOTTOM LIFECYCLE TIMELINE     -->
        <!-- ============================================== -->
        <section class="lc-col" aria-label="Lifecycle steps">
            <div class="lc-col-head">
                <div class="card-title">
                    <i class="bi bi-list-ol"></i>
                    <span>Lifecycle Timeline</span>
                </div>
                <span class="chip gold tnum" style="height:22px;font-size:11px">{{ $doneCount }} / {{ $product->stages->count() }}</span>
            </div>

            <div class="lc-col-body">
                <div class="tl">
                    @foreach ($product->stages as $st)
                        @php
                            $isSel = ($selectedStageId == $st->id);
                            $isDone = ($st->status === 'completed');
                            $isCur = ($st->status === 'in_progress');
                            $isFail = ($st->status === 'failed');
                            $stageLabor = $st->expenses->where('expense_type', 'labor')->sum('amount');
                            $stageHours = $st->stage_data['hours_spent'] ?? null;
                            $icon = $stageIcons[$st->step_number] ?? 'bi-circle';
                        @endphp

                        <button type="button"
                                wire:click="selectStage({{ $st->id }})"
                                class="tl-step {{ $isDone ? 'is-done' : '' }} {{ $isCur ? 'is-active' : '' }} {{ $isFail ? 'is-failed' : '' }} {{ !$isDone && !$isCur && !$isFail ? 'is-pending' : '' }} {{ $isSel ? 'is-selected' : '' }}"
                                aria-current="{{ $isSel ? 'true' : 'false' }}">
                            <div class="tl-node">
                                @if ($isDone)
                                    <i class="bi bi-check-lg"></i>
                                @elseif ($isFail)
                                    <i class="bi bi-exclamation"></i>
                                @else
                                    <i class="bi {{ $icon }}"></i>
                                @endif
                            </div>

                            <div style="min-width:0">
                                <div class="tl-name">
                                    <span class="tl-no">{{ str_pad($st->step_number, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $st->step_name }}</span>
                                </div>
                                <div class="tl-sub">
                                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                        {{ $st->assignedUser->name ?? ($isDone ? 'Completed' : ($isCur ? 'Active' : 'Pending')) }}
                                    </span>
                                    @if ($stageLabor > 0)
                                        <span class="money tnum">Rs {{ $rs($stageLabor) }}</span>
                                    @elseif ($stageHours)
                                        <span class="dim tnum">{{ $stageHours }}h</span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- COLUMN 2: CENTER SPECS, SOP & ACCRUED COST     -->
        <!-- ============================================== -->
        <section class="lc-col" aria-label="Stage details and specifications">
            <div class="lc-col-head">
                <div class="card-title">
                    <i class="bi bi-journal-text"></i>
                    <span>Stage {{ $selectedStage->step_number }} Blueprint</span>
                </div>
                <div>
                    @if ($stageDone)
                        <span class="chip green"><span class="dot"></span>Completed</span>
                    @elseif ($stageActive)
                        <span class="chip amber"><span class="dot"></span>In progress</span>
                    @elseif ($stageFailed)
                        <span class="chip red"><span class="dot"></span>QC review defect</span>
                    @else
                        <span class="chip"><span class="dot"></span>Pending</span>
                    @endif
                </div>
            </div>

            <div class="lc-col-body">

                <!-- Stage Hero -->
                <div class="stage-hero">
                    <div class="stage-big-no gold-text">{{ str_pad($selectedStage->step_number, 2, '0', STR_PAD_LEFT) }}</div>
                    <div style="min-width:0">
                        <h2>{{ $selectedStage->step_name }}</h2>
                        <p>{{ $standardStages[$selectedStage->step_number]['description'] ?? 'Precision jewellery fabrication standard operating procedure.' }}</p>
                        <div class="stage-times">
                            <span>Artisan: <b>{{ $selectedStage->assignedUser->name ?? 'Unassigned' }}</b></span>
                            @if ($selectedStage->started_at)
                                <span>Started: <b>{{ $selectedStage->started_at->format('d M, H:i') }}</b></span>
                            @endif
                            @if ($selectedStage->completed_at)
                                <span>Completed: <b>{{ $selectedStage->completed_at->format('d M, H:i') }}</b></span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3D CAD Blueprint / Design Visualizer Card -->
                <div class="blueprint-card">
                    <div class="blueprint-img-wrap">
                        @if ($product->image_url)
                            <img src="{{ asset($product->image_url) }}" alt="3D CAD Blueprint - {{ $product->title }}">
                        @else
                            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 20px;text-align:center;color:var(--text-dim);gap:8px">
                                <i class="bi bi-camera" style="font-size:30px;color:var(--gold-soft)"></i>
                                <span style="font-size:13px;font-weight:600;color:var(--text-muted)">No 3D render or bench photo uploaded</span>
                                <span style="font-size:11px">Upload a CAD render, wax snapshot or finished photo for visual verification</span>
                            </div>
                        @endif
                    </div>
                    <div class="blueprint-bar">
                        <div style="display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--text)">
                            <i class="bi bi-layers-half" style="color:var(--gold)"></i>
                            <span>Design Blueprint</span>
                            @if ($product->image_url)
                                <span class="chip gold" style="height:18px;font-size:10px;padding:0 6px">Verified CAD</span>
                            @endif
                        </div>
                        <div style="display:flex;align-items:center;gap:8px">
                            <label class="btn btn-ghost btn-sm" style="cursor:pointer;position:relative;margin:0">
                                <input type="file" wire:model="new_design_image" accept="image/*" style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>{{ $product->image_url ? 'Replace' : 'Upload' }}</span>
                            </label>
                            @if ($product->image_url)
                                <button type="button" class="btn btn-ghost btn-sm" wire:click="removeDesignImage" title="Remove image" style="color:var(--rose);padding:0 8px">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    <div wire:loading wire:target="new_design_image" style="padding:6px 14px;background:rgba(217,180,90,0.1);font-size:11px;color:var(--gold);display:flex;align-items:center;gap:6px">
                        <i class="bi bi-arrow-repeat spin"></i> Uploading design blueprint…
                    </div>
                    @error('new_design_image')
                        <div style="padding:6px 14px;background:rgba(244,63,94,0.1);font-size:11px;color:var(--rose)">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Specifications Tiles -->
                <div class="block">
                    <div class="section-label">Tolerances & Target</div>
                    <div class="tiles">
                        <div class="tile">
                            <span class="label">Target Alloy</span>
                            <div class="val">{{ $product->metal_type }}</div>
                        </div>
                        <div class="tile">
                            <span class="label">Target Weight</span>
                            <div class="val tnum">{{ number_format($product->target_metal_weight, 3) }} g</div>
                        </div>
                        <div class="tile">
                            <span class="label">Spot Gold Rate</span>
                            <div class="val tnum">Rs {{ $rs($product->metal_unit_price_per_g) }}/g</div>
                        </div>
                        <div class="tile">
                            <span class="label">Tooling</span>
                            <div class="val">{{ $product->is_master_piece_required ? 'Rubber Mold' : 'Direct Cast' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Allocated Gemstones List -->
                @if ($product->stones->count() > 0)
                    <div class="block">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                            <div class="section-label" style="margin:0">Allocated Gemstones ({{ $product->stones->count() }})</div>
                            <span class="chip blue tnum" style="height:20px;font-size:10.5px">{{ $product->stones->sum('carat_weight') }} ctw total</span>
                        </div>

                        <div class="gem-list">
                            @foreach ($product->stones as $stn)
                                <div class="gem">
                                    <div class="gem-icon"><i class="bi bi-gem"></i></div>
                                    <div style="min-width:0">
                                        <div class="gem-name">{{ $stn->quantity }}× {{ $stn->stone_type }}</div>
                                        <div class="gem-meta">
                                            <span>{{ $stn->cut_shape }}</span>
                                            @if ($stn->dimensions_mm) <span>· {{ $stn->dimensions_mm }}</span> @endif
                                            <span>· <b class="tnum">{{ $stn->carat_weight }} ct</b></span>
                                            <span>· <span class="chip" style="height:18px;font-size:10px">{{ $stn->setting_method }}</span></span>
                                        </div>
                                    </div>
                                    <div>
                                        @if ($stn->is_set)
                                            <span class="chip green" style="height:22px"><i class="bi bi-check-lg"></i> Set</span>
                                        @else
                                            <span class="chip" style="height:22px">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stage QC Checkpoints Checklist -->
                @if (count($stageChecks) > 0)
                    <div class="block">
                        <div class="section-label">Quality Checkpoints</div>
                        <div class="check-list">
                            @foreach ($stageChecks as $cIdx => $cText)
                                <label class="check">
                                    <input type="checkbox" wire:model="checkpoints.{{ $cIdx }}">
                                    <span class="check-text">{{ $cText }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Visual Cost Breakdown Bar -->
                <div class="block">
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div class="section-label" style="margin:0">Accrued Cost Composition</div>
                        <span class="money gold"><span class="cur">Rs</span>{{ $rs($product->total_manufacturing_cost) }}</span>
                    </div>

                    <div class="cost-bar" title="Metal {{ $pctMetal }}% | Stones {{ $pctStone }}% | Labor {{ $pctLabor }}% | Consumables {{ $pctCons }}% | Loss {{ $pctLoss }}%">
                        <span class="c-metal" style="width: {{ $pctMetal }}%"></span>
                        <span class="c-stone" style="width: {{ $pctStone }}%"></span>
                        <span class="c-labor" style="width: {{ $pctLabor }}%"></span>
                        <span class="c-cons" style="width: {{ $pctCons }}%"></span>
                        @if ($pctLoss > 0)
                            <span class="c-loss" style="width: {{ $pctLoss }}%"></span>
                        @endif
                    </div>

                    <div class="cost-legend">
                        <div><span class="sw c-metal">Raw metal</span><span class="money tnum">Rs {{ $rs($product->total_metal_cost) }}</span></div>
                        <div><span class="sw c-stone">Gemstones</span><span class="money tnum">Rs {{ $rs($product->total_stone_cost) }}</span></div>
                        <div><span class="sw c-labor">Artisan labour</span><span class="money tnum">Rs {{ $rs($product->total_labor_cost) }}</span></div>
                        <div><span class="sw c-cons">Consumables</span><span class="money tnum">Rs {{ $rs($product->total_expenses_cost) }}</span></div>
                        <div style="grid-column:span 2;border-top:1px dashed var(--line);padding-top:6px">
                            <span class="sw c-loss" style="color:var(--red)">Metal loss scrap</span>
                            <span class="money tnum" style="color:var(--red)">Rs {{ $rs($product->total_metal_loss_cost) }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ============================================== -->
        <!-- COLUMN 3: RIGHT PANEL (INPUTS, EXPENSES & ACT) -->
        <!-- ============================================== -->
        <section class="lc-col" aria-label="Stage action and cost entries">
            <div class="lc-col-head">
                <div class="card-title">
                    <i class="bi bi-pencil-square"></i>
                    <span>Stage Inputs & Expenses</span>
                </div>
                <span class="chip gold tnum" style="height:22px;font-size:11px">Step {{ $selectedStage->step_number }}</span>
            </div>

            <div class="lc-col-body">

                <!-- Assigned Artisan -->
                <div class="group">
                    <div class="group-head">
                        <span class="group-title"><i class="bi bi-person-badge"></i> Assigned Artisan *</span>
                        <span class="dim" style="font-size:11px">{{ $standardStages[$selectedStage->step_number]['worker_role'] ?? 'Artisan' }}</span>
                    </div>

                    <div class="field">
                        <select class="select" wire:model="assigned_user_id" required aria-label="Assigned artisan">
                            <option value="">Select artisan…</option>
                            @foreach ($artisans as $art)
                                <option value="{{ $art->id }}">{{ $art->name }} ({{ $art->role }})</option>
                            @endforeach
                        </select>
                        @error('assigned_user_id') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Stage Specific Variables (from stageFields) -->
                @if (count($stageFields) > 0)
                    <div class="group">
                        <div class="group-head">
                            <span class="group-title"><i class="bi bi-sliders"></i> Stage Variables</span>
                            <span class="chip" style="height:20px;font-size:10.5px">Custom specs</span>
                        </div>

                        <div class="grid-2">
                            @foreach ($stageFields as $key => [$label, $type, $unit, $options])
                                <div class="field">
                                    <label for="fld-{{ $key }}">{{ $label }}</label>
                                    @if ($type === 'select')
                                        <select id="fld-{{ $key }}" class="select select-sm" wire:model="stageData.{{ $key }}">
                                            <option value="">Select…</option>
                                            @foreach ($options as $opt)
                                                <option value="{{ $opt }}">{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($unit)
                                        <div class="input-wrap">
                                            <input id="fld-{{ $key }}" type="number" step="0.01" class="input input-sm" wire:model="stageData.{{ $key }}">
                                            <span class="unit">{{ $unit }}</span>
                                        </div>
                                    @else
                                        <input id="fld-{{ $key }}" type="{{ $type }}" class="input input-sm" wire:model="stageData.{{ $key }}">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Metal Scale Tracking (for casting, filing, polishing) -->
                @if ($tracksMetal)
                    <div class="group">
                        <div class="group-head">
                            <span class="group-title"><i class="bi bi-scale"></i> Metal Chain of Custody</span>
                            <span class="chip gold" style="height:20px;font-size:10px">0.001g scale</span>
                        </div>

                        <div class="grid-2">
                            <div class="field">
                                <label for="m-in">Input Metal Weight</label>
                                <div class="input-wrap">
                                    <input id="m-in" type="number" step="0.001" min="0" class="input input-sm" wire:model.live.debounce.300ms="metal_input_weight_g">
                                    <span class="unit">g</span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="m-out">Output Piece Weight</label>
                                <div class="input-wrap">
                                    <input id="m-out" type="number" step="0.001" min="0" class="input input-sm" wire:model.live.debounce.300ms="metal_output_weight_g">
                                    <span class="unit">g</span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="m-scrap">Recovered Sprues / Scrap</label>
                                <div class="input-wrap">
                                    <input id="m-scrap" type="number" step="0.001" min="0" class="input input-sm" wire:model.live.debounce.300ms="metal_scrap_g">
                                    <span class="unit">g</span>
                                </div>
                            </div>
                            <div class="field">
                                <label for="m-dust">Filing / Buffing Dust</label>
                                <div class="input-wrap">
                                    <input id="m-dust" type="number" step="0.001" min="0" class="input input-sm" wire:model.live.debounce.300ms="metal_dust_g">
                                    <span class="unit">g</span>
                                </div>
                            </div>
                        </div>

                        <!-- Live Net Loss Meter -->
                        <div class="loss-meter {{ $this->netLoss > 0 ? 'is-loss' : '' }}">
                            <div class="row">
                                <span class="muted" style="font-size:12px;font-weight:600">Net Metal Loss:</span>
                                <span class="big tnum">{{ number_format($this->netLoss, 3) }} g <small style="font-size:13px">({{ $this->lossPercentage }}%)</small></span>
                            </div>
                            <div class="row" style="margin-top:4px">
                                <span class="dim" style="font-size:11.5px">Loss write-off value:</span>
                                <span class="money tnum" style="font-size:14px;color:inherit">Rs {{ $rs($this->lossCost) }}</span>
                            </div>
                            <div class="gauge">
                                <span style="width: {{ min(100, $this->lossPercentage * 10) }}%"></span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Labor & Direct Expenses -->
                <div class="group">
                    <div class="group-head">
                        <span class="group-title"><i class="bi bi-wallet2"></i> Time & Direct Expenses</span>
                        <span class="dim" style="font-size:11px">Stage cost</span>
                    </div>

                    <div class="grid-3">
                        <div class="field">
                            <label for="e-hours">Artisan Hours</label>
                            <div class="input-wrap">
                                <input id="e-hours" type="number" step="0.25" min="0" class="input input-sm" wire:model="hours_spent">
                                <span class="unit">hrs</span>
                            </div>
                        </div>
                        <div class="field">
                            <label for="e-labor">Labour Pay</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="e-labor" type="number" step="50" min="0" class="input input-sm" wire:model="labor_cost">
                            </div>
                        </div>
                        <div class="field">
                            <label for="e-cons">Consumables / Gas</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="e-cons" type="number" step="50" min="0" class="input input-sm" wire:model="consumables_cost">
                            </div>
                        </div>
                    </div>

                    <div class="stage-total">
                        <span class="muted">Stage Subtotal (Labour + Consumables):</span>
                        <span class="money gold"><span class="cur">Rs</span>{{ $rs((float)$labor_cost + (float)$consumables_cost) }}</span>
                    </div>
                </div>

                <!-- Stone Setting Confirmation (if stage 4 or 12) -->
                @if ($setsStones && $product->stones->count() > 0)
                    <div class="group">
                        <div class="group-head">
                            <span class="group-title"><i class="bi bi-gem"></i> Mark Stones as Set</span>
                            <span class="chip blue" style="height:20px;font-size:10px">Stage {{ $selectedStage->step_number }}</span>
                        </div>

                        <div class="check-list">
                            @foreach ($product->stones as $stn)
                                <label class="check">
                                    <input type="checkbox" value="{{ $stn->id }}" wire:model="selectedStoneIds">
                                    <span class="check-text">
                                        <b>{{ $stn->quantity }}× {{ $stn->stone_type }}</b> ({{ $stn->carat_weight }}ct) · {{ $stn->setting_method }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stage Observations & Notes -->
                <div class="group" style="margin-bottom:0">
                    <div class="group-head">
                        <span class="group-title"><i class="bi bi-chat-left-text"></i> Bench Notes & QC Observations</span>
                    </div>
                    <textarea class="textarea" wire:model="stage_notes" rows="3" placeholder="Porosity, prong alignment, kiln temp, or special instructions…"></textarea>
                </div>

            </div>

            <!-- Footer Action Buttons -->
            <div class="lc-col-foot">
                @if ($stageFailed)
                    <div class="notice red" style="margin-bottom:4px">
                        <i class="bi bi-exclamation-octagon"></i>
                        <span>Defect flagged for this stage. Rework or recast required.</span>
                    </div>
                    <button type="button" class="btn btn-gold btn-block" wire:click="resumeStage">
                        <i class="bi bi-arrow-clockwise"></i> Resume & Rework Stage
                    </button>
                @else
                    <button type="button" class="btn btn-gold btn-block" wire:click="completeAndAdvanceStage" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="completeAndAdvanceStage"><i class="bi bi-check2-circle"></i> Complete & advance stage</span>
                        <span wire:loading wire:target="completeAndAdvanceStage">Advancing…</span>
                    </button>
                @endif

                <div style="display:grid;grid-template-columns:1fr auto;gap:8px">
                    <button type="button" class="btn btn-ghost" wire:click="saveStage" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveStage"><i class="bi bi-save"></i> Save progress</span>
                        <span wire:loading wire:target="saveStage">Saving…</span>
                    </button>

                    @unless ($stageFailed)
                        <button type="button" class="btn btn-danger" wire:click="markDefect" title="Flag defect / send for rework">
                            <i class="bi bi-exclamation-triangle"></i> Flag defect
                        </button>
                    @endunless
                </div>
            </div>
        </section>

    </div>

</div>
