<div class="p-4">

    <!-- Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-warning"></i>
                Manufacturing Expenses & Costing Audits
            </h2>
            <p class="text-muted small mb-0">Reconciliation of artisan wages, consumables, plating chemicals, and precious metal casting losses.</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Artisan Labor Paid</div>
                <div class="fs-4 fw-bold text-dark mt-1">Rs. {{ number_format($totalLabor, 2) }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-people me-1"></i> Piece-rate wages</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Metal Loss Write-Offs</div>
                <div class="fs-4 fw-bold text-danger mt-1">Rs. {{ number_format($totalMetalLoss, 2) }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-fire me-1"></i> Casting & buffing loss</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Consumables & Gas</div>
                <div class="fs-4 fw-bold text-warning mt-1">Rs. {{ number_format($totalConsumables, 2) }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-droplet me-1"></i> Wax, plaster, plating</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                <div class="text-muted small fw-semibold text-uppercase">Total Manufacturing Expenses</div>
                <div class="fs-4 fw-bold text-primary mt-1">Rs. {{ number_format($grandTotal, 2) }}</div>
                <div class="small text-secondary mt-1"><i class="bi bi-cash-stack me-1"></i> All jobs combined</div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-start-0" placeholder="Search description, job SKU, piece name...">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select wire:model.live="expenseType" class="form-select bg-light">
                        <option value="">All Expense Types</option>
                        <option value="labor">Artisan Labor</option>
                        <option value="consumable">Consumables & Supplies</option>
                        <option value="energy">Kiln Energy / Gas</option>
                        <option value="plating">Rhodium Plating</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="ps-3 py-3">Date</th>
                        <th class="py-3">Jewelry Piece / SKU</th>
                        <th class="py-3">Stage</th>
                        <th class="py-3">Category</th>
                        <th class="py-3">Description</th>
                        <th class="py-3 text-end pe-3">Amount (Rs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr>
                            <td class="ps-3 small text-muted">
                                {{ $exp->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <a href="{{ route('production.lifecycle', $exp->product_id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $exp->product->title ?? 'N/A' }}
                                </a>
                                <div class="font-monospace small text-muted">{{ $exp->product->sku ?? '' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $exp->stage->step_number ?? '' }}. {{ $exp->stage->step_name ?? 'General' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge @if($exp->expense_type === 'labor') bg-primary-subtle text-primary @elseif($exp->expense_type === 'consumable') bg-warning-subtle text-warning @else bg-secondary-subtle text-secondary @endif text-uppercase">
                                    {{ $exp->expense_type }}
                                </span>
                            </td>
                            <td class="small text-secondary">{{ $exp->description }}</td>
                            <td class="text-end pe-3 fw-bold font-monospace text-dark">
                                Rs. {{ number_format($exp->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-2 text-muted"></i>
                                <div class="mt-2">No production expenses logged yet. Complete lifecycle steps to record artisan labor and consumables.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-3 border-top">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

</div>
