<div>
    
    <!-- Top Job Summary Bar -->
    <div class="bg-white border-bottom px-4 py-3 shadow-sm">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('production.products') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                        <i class="bi bi-arrow-left"></i> All Jobs
                    </a>
                    <span class="badge bg-dark font-monospace">{{ $product->sku }}</span>
                    <span class="badge @if($product->status === 'completed') bg-success @elseif($product->status === 'qc_review') bg-danger @else bg-warning text-dark @endif text-uppercase" style="font-size: 0.7rem;">
                        {{ str_replace('_', ' ', $product->status) }}
                    </span>
                    @if($product->is_master_piece_required)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;">
                            <i class="bi bi-box me-1"></i> MASTER MOLD
                        </span>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    {{ $product->title }}
                    <span class="badge bg-light text-secondary border fw-normal fs-6">{{ $product->category }}</span>
                </h4>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-4">
                <div class="text-end">
                    <div class="text-muted small">Metal & Target Weight</div>
                    <div class="fw-bold text-dark">
                        <i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.6rem;"></i>
                        {{ $product->metal_type }} ({{ number_format($product->target_metal_weight, 2) }}g)
                    </div>
                </div>

                <div class="text-end">
                    <div class="text-muted small">Accrued Manufacturing Cost</div>
                    <div class="fw-bold text-primary font-monospace fs-5">
                        Rs. {{ number_format($product->total_manufacturing_cost, 2) }}
                    </div>
                </div>

                <div class="text-end">
                    <div class="text-muted small">Suggested Retail Price (MSRP)</div>
                    <div class="fw-bold text-success font-monospace fs-5">
                        Rs. {{ number_format($product->recommended_selling_price, 2) }}
                    </div>
                </div>
            </div>
        </div>

        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3 mb-0 py-2 small" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3 mb-0 py-2 small" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- 3-COLUMN WORKSPACE CONTAINER -->
    <div class="container-fluid px-3 py-3">
        <div class="row g-3">
            
            <!-- ========================================== -->
            <!-- COLUMN 1: LEFT SIDE (TOP-TO-BOTTOM LIFECYCLE STEPS) -->
            <!-- ========================================== -->
            <div class="col-12 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small text-uppercase tracking-wider">
                            <i class="bi bi-list-ol text-warning me-1"></i> Production Steps
                        </span>
                        <span class="badge bg-secondary-subtle text-secondary small">
                            {{ $product->stages->where('status', 'completed')->count() }} / {{ $product->stages->count() }} Done
                        </span>
                    </div>

                    <div class="card-body p-2" style="max-height: calc(100vh - 220px); overflow-y: auto;">
                        <div class="d-flex flex-column gap-2">
                            @foreach($product->stages as $stage)
                                @php
                                    $isSelected = ($selectedStageId == $stage->id);
                                    $isDone = ($stage->status === 'completed');
                                    $isCurrent = ($stage->status === 'in_progress');
                                    $isFailed = ($stage->status === 'failed');
                                @endphp
                                <div wire:click="selectStage({{ $stage->id }})"
                                     class="p-2 rounded-3 border transition-all cursor-pointer @if($isSelected) bg-warning-subtle border-warning shadow-sm @else bg-light border-light-subtle @endif"
                                     style="cursor: pointer;">
                                    <div class="d-flex align-items-start gap-2">
                                        <!-- Step Icon Status -->
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white mt-1 flex-shrink-0"
                                             style="width: 24px; height: 24px; font-size: 0.75rem;
                                                    background-color: @if($isDone) #198754 @elseif($isCurrent) #d4af37 @elseif($isFailed) #dc3545 @else #6c757d @endif;">
                                            @if($isDone)
                                                <i class="bi bi-check-lg"></i>
                                            @elseif($isCurrent)
                                                <i class="bi bi-play-fill"></i>
                                            @elseif($isFailed)
                                                <i class="bi bi-exclamation"></i>
                                            @else
                                                {{ $stage->step_number }}
                                            @endif
                                        </div>

                                        <!-- Step Details -->
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-bold text-dark small text-truncate">
                                                    {{ $stage->step_number }}. {{ $stage->step_name }}
                                                </span>
                                                @if($isCurrent)
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">ACTIVE</span>
                                                @elseif($isDone)
                                                    <span class="badge bg-success-subtle text-success" style="font-size: 0.6rem;">DONE</span>
                                                @elseif($isFailed)
                                                    <span class="badge bg-danger text-white" style="font-size: 0.6rem;">DEFECT</span>
                                                @endif
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between text-muted mt-1" style="font-size: 0.72rem;">
                                                <span>
                                                    <i class="bi bi-person me-1"></i>
                                                    {{ $stage->assignedUser->name ?? 'Unassigned' }}
                                                </span>
                                                @php $stageExpenseTotal = $stage->expenses->sum('amount'); @endphp
                                                @if($stageExpenseTotal > 0)
                                                    <span class="fw-semibold text-secondary">Rs. {{ number_format($stageExpenseTotal, 0) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- COLUMN 2: MIDDLE (STAGE DETAILS, SPECS & ACCRUED COST) -->
            <!-- ========================================== -->
            <div class="col-12 col-md-8 col-xl-5">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="badge bg-warning text-dark me-2">Stage {{ $selectedStage->step_number }}</span>
                            <span class="fw-bold text-dark fs-6">{{ $selectedStage->step_name }}</span>
                        </div>
                        <span class="badge @if($selectedStage->status === 'completed') bg-success @elseif($selectedStage->status === 'in_progress') bg-warning text-dark @else bg-secondary @endif">
                            {{ strtoupper(str_replace('_', ' ', $selectedStage->status)) }}
                        </span>
                    </div>

                    <div class="card-body p-3" style="max-height: calc(100vh - 220px); overflow-y: auto;">
                        
                        <!-- Stage Blueprint & SOP Description -->
                        <div class="alert alert-light border mb-3 p-3">
                            <div class="d-flex align-items-center gap-2 mb-1 text-primary fw-semibold small">
                                <i class="bi bi-journal-text"></i> Standard Operating Guidelines (SOP)
                            </div>
                            <p class="small text-muted mb-0">
                                {{ $standardStages[$selectedStage->step_number]['description'] ?? 'Perform precision manufacturing work according to customer tolerances.' }}
                            </p>
                        </div>

                        <!-- Product & Alloy Specifications Card -->
                        <div class="card border rounded-3 p-3 mb-3 bg-light-subtle">
                            <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-sliders text-warning"></i> Specifications & Tolerances
                            </h6>
                            <div class="row g-2 small">
                                <div class="col-6">
                                    <span class="text-muted d-block">Target Alloy:</span>
                                    <strong class="text-dark">{{ $product->metal_type }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Target Finished Weight:</span>
                                    <strong class="text-dark">{{ number_format($product->target_metal_weight, 3) }} grams</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Gold Base Spot Rate:</span>
                                    <strong class="text-dark">Rs. {{ number_format($product->metal_unit_price_per_g, 2) }} / g</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block">Mold Requirement:</span>
                                    <strong class="text-dark">{{ $product->is_master_piece_required ? 'Vulcanized Rubber Mold' : 'Direct Cast Resin' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Allocated Gemstones List -->
                        <div class="card border rounded-3 p-3 mb-3 bg-white">
                            <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center justify-content-between">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="bi bi-gem text-info"></i> Allocated Gemstones ({{ $product->stones->count() }})
                                </span>
                                <span class="badge bg-light text-dark border">
                                    {{ $product->stones->sum('carat_weight') }} ctw
                                </span>
                            </h6>

                            @if($product->stones->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle small mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Stone Variety</th>
                                                <th>Dimensions</th>
                                                <th>Qty</th>
                                                <th>Carat</th>
                                                <th>Setting</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->stones as $stone)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $stone->stone_type }} ({{ $stone->cut_shape }})</td>
                                                    <td>{{ $stone->dimensions_mm ?: '-' }}</td>
                                                    <td>{{ $stone->quantity }}</td>
                                                    <td>{{ $stone->carat_weight }} ct</td>
                                                    <td><span class="badge bg-light text-secondary border">{{ $stone->setting_method }}</span></td>
                                                    <td>
                                                        @if($stone->is_set)
                                                            <span class="badge bg-success-subtle text-success"><i class="bi bi-check"></i> Set</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-muted small fst-italic">No stones allocated for this piece.</div>
                            @endif
                        </div>

                        <!-- Standard Quality Checkpoints Checklist -->
                        <div class="card border rounded-3 p-3 mb-3 bg-white">
                            <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-2">
                                <i class="bi bi-check2-square text-success"></i> Stage QC Checkpoints
                            </h6>
                            <div class="d-flex flex-column gap-2 small">
                                @php
                                    $defaultChecks = [
                                        1 => ['Dimensions & prong heights verified in CAD', 'Digital render approved by customer'],
                                        2 => ['Clean resin print without layer lines', 'Vulcanized rubber cut with crisp parting lines'],
                                        3 => ['Vacuum injection without air bubbles', 'Wax replica weight corresponds to gold conversion'],
                                        4 => ['Gems seated level in wax prongs', 'No wax flash covering stone tables'],
                                        5 => ['Main sprue angled at 45 degrees', 'Clearance between tree pieces > 5mm'],
                                        6 => ['Plaster mixed with deionized water under vacuum', 'Debubbled for full 2 minutes'],
                                        7 => ['Full 10-hour burnout ramp completed', 'Flask face free of investment debris'],
                                        8 => ['Induction melt vacuum reached 29 inHg', 'Crucible fluxed with clean borax'],
                                        9 => ['Quench timing respected to prevent thermal shock', 'Pickling acid ultrasonic clean done'],
                                        10 => ['Sprues cleanly separated with jeweler saw', 'Magnified surface inspection for porosity'],
                                        11 => ['Sprue stubs filed flush without pitting', 'Ring mandril size checked with gauge'],
                                        12 => ['Prongs seated tightly without rocking', 'Stone tables level and prongs polished'],
                                        13 => ['Tripoli prep-polish buffed all firescale', 'White Rouge mirror luster achieved'],
                                        14 => ['Bath temperature and voltage calibrated', 'Uniform micro-rhodium coating verified'],
                                        15 => ['Laser hallmark stamped (750 / 925)', 'Certificate printed & piece transferred to vault'],
                                    ];
                                    $stepChecks = $defaultChecks[$selectedStage->step_number] ?? ['Standard quality check completed'];
                                @endphp

                                @foreach($stepChecks as $chkIndex => $chkText)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="chk_{{ $chkIndex }}" wire:model="checkpoints.{{ $chkIndex }}">
                                        <label class="form-check-label text-dark" for="chk_{{ $chkIndex }}">
                                            {{ $chkText }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Accrued Cost Breakdown Widget -->
                        <div class="card border-0 bg-primary-subtle text-primary-emphasis rounded-3 p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold small text-uppercase">Accrued Cost Breakdown</span>
                                <span class="fw-bold fs-6">Rs. {{ number_format($product->total_manufacturing_cost, 2) }}</span>
                            </div>
                            <div class="row g-2 small text-secondary">
                                <div class="col-6">Metal: Rs. {{ number_format($product->total_metal_cost, 2) }}</div>
                                <div class="col-6">Gemstones: Rs. {{ number_format($product->total_stone_cost, 2) }}</div>
                                <div class="col-6">Labor: Rs. {{ number_format($product->total_labor_cost, 2) }}</div>
                                <div class="col-6">Consumables: Rs. {{ number_format($product->total_expenses_cost, 2) }}</div>
                                <div class="col-12 border-top pt-1 mt-1 text-danger fw-semibold">
                                    Metal Loss Scrap: Rs. {{ number_format($product->total_metal_loss_cost, 2) }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- COLUMN 3: RIGHT SIDE (ACTION, VARIABLES, EXPENSES & COMPLETION) -->
            <!-- ========================================== -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                    <div class="card-header bg-white border-bottom py-3 px-3 d-flex align-items-center justify-content-between">
                        <span class="fw-bold text-dark small text-uppercase tracking-wider">
                            <i class="bi bi-pencil-square text-warning me-1"></i> Stage Inputs & Cost Action
                        </span>
                        <span class="badge bg-light text-dark border">
                            Step {{ $selectedStage->step_number }} / {{ $product->stages->count() }}
                        </span>
                    </div>

                    <div class="card-body p-3" style="max-height: calc(100vh - 220px); overflow-y: auto;">
                        <form wire:submit.prevent="completeAndAdvanceStage">

                            <!-- Assigned Artisan Dropdown -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">
                                    Assigned Artisan / Worker *
                                </label>
                                <select wire:model="assigned_user_id" class="form-select" required>
                                    <option value="">Select Specialized Artisan...</option>
                                    @foreach($artisans as $artisan)
                                        <option value="{{ $artisan->id }}">{{ $artisan->name }} ({{ $artisan->role }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Precision Metal Scale Tracking (for casting, filing, polishing) -->
                            <div class="card border rounded-3 p-3 mb-3 bg-light">
                                <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center justify-content-between">
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="bi bi-scale text-warning"></i> Metal Weight Scale (grams)
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;">0.001g Precision</span>
                                </h6>

                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <label class="form-label" style="font-size: 0.72rem;">Input Metal Weight (g)</label>
                                        <input type="number" step="0.001" wire:model.live="metal_input_weight_g" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" style="font-size: 0.72rem;">Output Piece Weight (g)</label>
                                        <input type="number" step="0.001" wire:model.live="metal_output_weight_g" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" style="font-size: 0.72rem;">Scrap / Sprues Recovered (g)</label>
                                        <input type="number" step="0.001" wire:model.live="metal_scrap_g" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" style="font-size: 0.72rem;">Filing / Buffing Dust (g)</label>
                                        <input type="number" step="0.001" wire:model.live="metal_dust_g" class="form-control form-control-sm">
                                    </div>
                                </div>

                                <!-- Live Net Loss Calculation Box -->
                                <div class="p-2 rounded-2 @if($this->netLoss > 0) bg-danger-subtle text-danger border border-danger-subtle @else bg-success-subtle text-success border border-success-subtle @endif">
                                    <div class="d-flex align-items-center justify-content-between small fw-bold">
                                        <span>Net Metal Loss:</span>
                                        <span>{{ number_format($this->netLoss, 3) }} g ({{ $this->lossPercentage }}%)</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between small">
                                        <span>Loss Write-Off Value:</span>
                                        <span class="font-monospace fw-bold">Rs. {{ number_format($this->lossCost, 2) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Direct Stage Expenses (Labor & Consumables) -->
                            <div class="card border rounded-3 p-3 mb-3 bg-white">
                                <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-cash-coin text-success"></i> Direct Stage Expenses
                                </h6>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label" style="font-size: 0.72rem;">Artisan Labor Pay (Rs.)</label>
                                        <input type="number" step="50" wire:model="labor_cost" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label" style="font-size: 0.72rem;">Consumables / Gas / Plating</label>
                                        <input type="number" step="50" wire:model="consumables_cost" class="form-control form-control-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Gemstone Setting Action (if step is 4 or 12) -->
                            @if(in_array($selectedStage->step_number, [4, 12]) && $product->stones->count() > 0)
                                <div class="card border rounded-3 p-3 mb-3 bg-white">
                                    <h6 class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-gem text-info"></i> Mark Stones as Set in this Step
                                    </h6>
                                    <div class="d-flex flex-column gap-1 small">
                                        @foreach($product->stones as $stone)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="{{ $stone->id }}" id="stone_chk_{{ $stone->id }}" wire:model="selectedStoneIds">
                                                <label class="form-check-label text-dark" for="stone_chk_{{ $stone->id }}">
                                                    {{ $stone->quantity }}x {{ $stone->stone_type }} ({{ $stone->carat_weight }}ct) - {{ $stone->setting_method }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Stage Observations & QC Notes -->
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-dark">
                                    Stage Notes & Observations
                                </label>
                                <textarea wire:model="stage_notes" class="form-control form-control-sm" rows="3" placeholder="Flask condition, sprue fill, surface luster, or special customer instructions..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-warning fw-semibold py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #e6ca65 0%, #d4af37 100%); border: none; color: #111;">
                                    <i class="bi bi-check2-circle fs-5"></i>
                                    <span>COMPLETE & ADVANCE STEP</span>
                                </button>

                                <button type="button" wire:click="markDefect('Casting defect / porosity flagged')" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-exclamation-octagon"></i>
                                    <span>Report Defect / Flag For Re-Cast</span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

</div>
