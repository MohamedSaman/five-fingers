<div class="p-4">

    <!-- Top Action Header & Metrics -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-gem text-warning"></i>
                Jewelry Manufacturing Jobs
            </h2>
            <p class="text-muted small mb-0">Track custom pieces, artisan stage assignments, precious metal loss, and real-time costs.</p>
        </div>

        <button wire:click="openModal" class="btn btn-warning fw-semibold px-4 py-2 d-flex align-items-center gap-2 shadow-sm" style="background: linear-gradient(135deg, #e6ca65 0%, #d4af37 100%); border: none; color: #111;">
            <i class="bi bi-plus-circle-fill"></i>
            <span>+ Create New Jewelry Job</span>
        </button>
    </div>

    <!-- Metric Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Active Production Jobs</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ \App\Models\ProductionProduct::where('status', 'in_production')->count() }} Pieces</div>
                    </div>
                    <div class="rounded-3 bg-warning-subtle text-warning p-3 fs-4">
                        <i class="bi bi-hammer"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Metal In Casting</div>
                        <div class="fs-4 fw-bold text-dark mt-1">{{ number_format(\App\Models\ProductionProduct::where('status', 'in_production')->sum('target_metal_weight'), 2) }} g</div>
                    </div>
                    <div class="rounded-3 bg-success-subtle text-success p-3 fs-4">
                        <i class="bi bi-pie-chart-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">WIP Accrued Cost</div>
                        <div class="fs-4 fw-bold text-primary mt-1">Rs. {{ number_format(\App\Models\ProductionProduct::where('status', 'in_production')->sum('total_manufacturing_cost'), 2) }}</div>
                    </div>
                    <div class="rounded-3 bg-primary-subtle text-primary p-3 fs-4">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Awaiting Final QC</div>
                        <div class="fs-4 fw-bold text-info mt-1">{{ \App\Models\ProductionProduct::where('status', 'qc_review')->count() }} Pieces</div>
                    </div>
                    <div class="rounded-3 bg-info-subtle text-info p-3 fs-4">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Search SKU, title, ring, necklace...">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select wire:model.live="statusFilter" class="form-select bg-light">
                        <option value="">All Statuses</option>
                        <option value="in_production">In Production</option>
                        <option value="qc_review">QC Review</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select wire:model.live="metalFilter" class="form-select bg-light">
                        <option value="">All Metal Alloys</option>
                        <option value="18K Yellow Gold">18K Yellow Gold</option>
                        <option value="18K White Gold">18K White Gold</option>
                        <option value="18K Rose Gold">18K Rose Gold</option>
                        <option value="22K Yellow Gold">22K Yellow Gold</option>
                        <option value="950 Platinum">950 Platinum</option>
                        <option value="925 Sterling Silver">925 Sterling Silver</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 text-md-end">
                    <span class="text-muted small">Showing {{ $products->total() }} total jewelry jobs</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3 py-3">Job / SKU</th>
                        <th class="py-3">Jewelry Piece & Specs</th>
                        <th class="py-3">Gemstones</th>
                        <th class="py-3">Lifecycle Progress</th>
                        <th class="py-3 text-end">Accrued Cost</th>
                        <th class="py-3 text-end">MSRP Price</th>
                        <th class="pe-3 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $totalStages = $product->stages->count();
                            $completedStages = $product->stages->where('status', 'completed')->count();
                            $percent = $totalStages > 0 ? round(($completedStages / $totalStages) * 100) : 0;
                            $currentStage = $product->stages->firstWhere('status', 'in_progress') ?? $product->stages->last();
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark font-monospace">{{ $product->sku }}</div>
                                <span class="badge @if($product->status === 'completed') bg-success-subtle text-success @elseif($product->status === 'qc_review') bg-info-subtle text-info @else bg-warning-subtle text-warning @endif rounded-pill px-2 py-1 mt-1">
                                    {{ ucwords(str_replace('_', ' ', $product->status)) }}
                                </span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark">{{ $product->title }}</div>
                                <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                                    <span class="badge bg-light text-dark border">{{ $product->category }}</span>
                                    <span><i class="bi bi-circle-fill text-warning me-1" style="font-size: 0.5rem;"></i>{{ $product->metal_type }} ({{ number_format($product->target_metal_weight, 2) }}g)</span>
                                </div>
                            </td>

                            <td>
                                @if($product->stones->count() > 0)
                                    <div class="small">
                                        @foreach($product->stones->take(2) as $stone)
                                            <div class="text-secondary">
                                                <i class="bi bi-gem me-1 text-primary"></i>
                                                {{ $stone->quantity }}x {{ $stone->stone_type }} ({{ $stone->carat_weight }}ct)
                                            </div>
                                        @endforeach
                                        @if($product->stones->count() > 2)
                                            <span class="text-muted fst-italic" style="font-size: 0.75rem;">+{{ $product->stones->count() - 2 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small fst-italic">Plain Metal / No Stones</span>
                                @endif
                            </td>

                            <td style="min-width: 220px;">
                                <div class="d-flex align-items-center justify-content-between small mb-1">
                                    <span class="fw-semibold text-dark">{{ $currentStage ? $currentStage->step_name : 'Planning' }}</span>
                                    <span class="text-muted">{{ $completedStages }}/{{ $totalStages }} ({{ $percent }}%)</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                            </td>

                            <td class="text-end">
                                <div class="fw-bold text-dark font-monospace">Rs. {{ number_format($product->total_manufacturing_cost, 2) }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    Loss: Rs. {{ number_format($product->total_metal_loss_cost, 2) }}
                                </div>
                            </td>

                            <td class="text-end">
                                <div class="fw-bold text-success font-monospace">Rs. {{ number_format($product->recommended_selling_price, 2) }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">+{{ $product->markup_percentage }}% markup</div>
                            </td>

                            <td class="pe-3 text-center">
                                <a href="{{ route('production.lifecycle', $product->id) }}" class="btn btn-sm btn-dark px-3 shadow-sm d-inline-flex align-items-center gap-1">
                                    <span>Open Lifecycle</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-gem text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="fw-semibold mt-3 text-dark">No Jewelry Jobs Found</h5>
                                    <p class="text-muted small">Create your first custom jewelry manufacturing job with our 15-stage pipeline.</p>
                                    <button wire:click="openModal" class="btn btn-warning btn-sm fw-semibold mt-2 px-3">
                                        + Create New Job
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-3 border-top">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE JEWELRY JOB MODAL -->
    @if($showCreateModal)
        <div class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.65);" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    
                    <!-- Modal Header -->
                    <div class="modal-header bg-dark text-white px-4 py-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-gem text-warning fs-5"></i>
                            <h5 class="modal-title fw-bold mb-0">Create New Jewelry Manufacturing Job</h5>
                        </div>
                        <button type="button" wire:click="closeModal" class="btn-close btn-close-white" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body p-4 bg-light">
                        <form wire:submit.prevent="createProduct">
                            
                            <!-- Section 1: Product & Metal Specifications -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    1. Design & Metal Specifications
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Job SKU / Code *</label>
                                        <input type="text" wire:model="sku" class="form-control font-monospace" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label small fw-semibold">Jewelry Item Title *</label>
                                        <input type="text" wire:model="title" class="form-control" placeholder="e.g. Royal Blue Sapphire Halo Ring" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Category *</label>
                                        <select wire:model="category" class="form-select">
                                            <option value="Ring">Ring</option>
                                            <option value="Necklace">Necklace</option>
                                            <option value="Pendant">Pendant</option>
                                            <option value="Earrings">Earrings</option>
                                            <option value="Bracelet">Bracelet</option>
                                            <option value="Bangle">Bangle</option>
                                            <option value="Custom Jewelry">Custom Jewelry</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Metal Alloy *</label>
                                        <select wire:model.live="metal_type" class="form-select">
                                            <option value="18K Yellow Gold">18K Yellow Gold (Au 750)</option>
                                            <option value="18K White Gold">18K White Gold (Au 750)</option>
                                            <option value="18K Rose Gold">18K Rose Gold (Au 750)</option>
                                            <option value="22K Yellow Gold">22K Yellow Gold (Au 916)</option>
                                            <option value="24K Pure Gold">24K Pure Gold (Au 999)</option>
                                            <option value="950 Platinum">950 Platinum (Pt 950)</option>
                                            <option value="925 Sterling Silver">925 Sterling Silver</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Estimated Target Weight (g) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.001" wire:model="target_metal_weight" class="form-control" required>
                                            <span class="input-group-text">grams</span>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Spot Gold Price / Gram (Rs.) *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number" step="100" wire:model="metal_unit_price_per_g" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label small fw-semibold">Target Markup %</label>
                                        <div class="input-group">
                                            <input type="number" step="1" wire:model="markup_percentage" class="form-control">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>

                                    <!-- Master Piece Mold Checkbox -->
                                    <div class="col-12 mt-3">
                                        <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between">
                                            <div>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" id="masterPieceSwitch" wire:model.live="is_master_piece_required">
                                                    <label class="form-check-label fw-bold text-dark ms-2" for="masterPieceSwitch">
                                                        Create Master Piece & Vulcanized Rubber Mold
                                                    </label>
                                                </div>
                                                <div class="text-muted small ms-4">
                                                    Check this if you plan to mass-produce or store a rubber mold for repeat injections. If unchecked, direct 3D castable resin printing will be used.
                                                </div>
                                            </div>
                                            <span class="badge @if($is_master_piece_required) bg-success @else bg-secondary @endif">
                                                {{ $is_master_piece_required ? 'MOLD ENABLED' : 'ONE-OFF CUSTOM' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Gemstones Required Table -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                        <i class="bi bi-gem text-info"></i>
                                        2. Gemstones Specification & Allocation
                                    </h6>
                                    <button type="button" wire:click="addStoneRow" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-plus"></i> Add Gemstone Row
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>Stone Variety</th>
                                                <th>Cut / Shape</th>
                                                <th>Dimensions</th>
                                                <th>Qty</th>
                                                <th>Carat Weight</th>
                                                <th>Setting Method</th>
                                                <th>Unit Cost (Rs.)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($stones as $index => $stone)
                                                <tr>
                                                    <td>
                                                        <input type="text" wire:model="stones.{{ $index }}.stone_type" class="form-control form-control-sm" placeholder="e.g. Blue Sapphire">
                                                    </td>
                                                    <td>
                                                        <select wire:model="stones.{{ $index }}.cut_shape" class="form-select form-select-sm">
                                                            <option value="Round Brilliant">Round Brilliant</option>
                                                            <option value="Oval">Oval</option>
                                                            <option value="Cushion">Cushion</option>
                                                            <option value="Princess">Princess</option>
                                                            <option value="Emerald Cut">Emerald Cut</option>
                                                            <option value="Pear">Pear</option>
                                                            <option value="Marquise">Marquise</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" wire:model="stones.{{ $index }}.dimensions_mm" class="form-control form-control-sm" placeholder="e.g. 7x5mm">
                                                    </td>
                                                    <td style="width: 80px;">
                                                        <input type="number" wire:model="stones.{{ $index }}.quantity" class="form-control form-control-sm" min="1">
                                                    </td>
                                                    <td style="width: 100px;">
                                                        <input type="number" step="0.01" wire:model="stones.{{ $index }}.carat_weight" class="form-control form-control-sm">
                                                    </td>
                                                    <td>
                                                        <select wire:model="stones.{{ $index }}.setting_method" class="form-select form-select-sm">
                                                            <option value="hand_prong">Hand Setting (Prong)</option>
                                                            <option value="wax_cast">Wax Setting (Cast-in-Place)</option>
                                                            <option value="bezel">Hand Setting (Bezel)</option>
                                                            <option value="pave">Micro Pavé</option>
                                                            <option value="channel">Channel</option>
                                                        </select>
                                                    </td>
                                                    <td style="width: 130px;">
                                                        <input type="number" wire:model="stones.{{ $index }}.stone_unit_cost" class="form-control form-control-sm">
                                                    </td>
                                                    <td class="text-center">
                                                        @if(count($stones) > 1)
                                                            <button type="button" wire:click="removeStoneRow({{ $index }})" class="btn btn-sm btn-link text-danger p-0">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Section 3: Select Required Stages (Checkboxes) -->
                            <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
                                <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                            <i class="bi bi-diagram-3 text-warning"></i>
                                            3. Manufacturing Lifecycle Stages
                                        </h6>
                                        <p class="text-muted small mb-0">Check the stages required for this piece. Unchecked stages will be omitted from the product life cycle.</p>
                                    </div>
                                    <span class="badge bg-dark">{{ count(array_filter($selectedStages)) }} of 15 Stages Selected</span>
                                </div>

                                <div class="row g-2">
                                    @foreach($standardStages as $stepNum => $stageMeta)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="p-2 border rounded-2 bg-light d-flex align-items-start gap-2 h-100">
                                                <input class="form-check-input mt-1" type="checkbox" id="stage_{{ $stepNum }}" wire:model="selectedStages.{{ $stepNum }}">
                                                <div class="flex-grow-1">
                                                    <label class="form-check-label fw-bold text-dark small d-block" for="stage_{{ $stepNum }}">
                                                        {{ $stepNum }}. {{ $stageMeta['name'] }}
                                                    </label>
                                                    <span class="text-muted d-block" style="font-size: 0.72rem; line-height: 1.3;">
                                                        {{ $stageMeta['description'] }}
                                                    </span>
                                                    <span class="badge bg-secondary-subtle text-secondary border mt-1" style="font-size: 0.65rem;">
                                                        <i class="bi bi-person me-1"></i>{{ $stageMeta['worker_role'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                                <button type="button" wire:click="closeModal" class="btn btn-secondary px-4">Cancel</button>
                                <button type="submit" class="btn btn-warning fw-semibold px-4 shadow-sm" style="background: linear-gradient(135deg, #e6ca65 0%, #d4af37 100%); border: none; color: #111;">
                                    <i class="bi bi-rocket-takeoff-fill me-1"></i> Create & Launch Production Lifecycle
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>
