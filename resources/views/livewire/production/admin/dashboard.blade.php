<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #d4af37;">Jewellery Production Management</h3>
            <p class="text-muted small m-0">Manufacturing batches, casting, gemstone settings, and artisan workshop tracking</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('production.admin.batches') }}" class="btn btn-warning text-dark fw-bold">
                <i class="bi bi-plus-circle me-1"></i> New Production Batch
            </a>
            <a href="{{ route('production.admin.materials') }}" class="btn btn-outline-dark">
                <i class="bi bi-box-seam me-1"></i> Raw Materials
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Active Batches</span>
                        <h4 class="fw-bold mt-1 mb-0">12</h4>
                        <small class="text-success"><i class="bi bi-arrow-up"></i> 3 in finishing</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-4">
                        <i class="bi bi-layers"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Gold in Production</span>
                        <h4 class="fw-bold mt-1 mb-0">340.5 g</h4>
                        <small class="text-muted">22k & 18k alloys</small>
                    </div>
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-4">
                        <i class="bi bi-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Stones Assigned</span>
                        <h4 class="fw-bold mt-1 mb-0">48 pcs</h4>
                        <small class="text-info">Sapphires & Diamonds</small>
                    </div>
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle fs-4">
                        <i class="bi bi-gem"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Artisans Working</span>
                        <h4 class="fw-bold mt-1 mb-0">6</h4>
                        <small class="text-success">All benches active</small>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle fs-4">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Production Batches Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Recent Jewellery Batches</h5>
            <a href="{{ route('production.admin.batches') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Batch Code</th>
                        <th>Jewellery Type</th>
                        <th>Alloy / Metal</th>
                        <th>Stones Included</th>
                        <th>Artisan / Goldsmith</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>#FF-B-101</strong></td>
                        <td>Sapphire Solitaire Ring</td>
                        <td>18K White Gold (4.2g)</td>
                        <td>1x Royal Blue (1.8ct)</td>
                        <td>Master Niflan</td>
                        <td><span class="badge bg-warning text-dark">Stone Setting</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline-secondary">Details</a></td>
                    </tr>
                    <tr>
                        <td><strong>#FF-B-102</strong></td>
                        <td>Pavé Diamond Bangle</td>
                        <td>22K Yellow Gold (18.5g)</td>
                        <td>24x Brilliant Cut (0.9ct)</td>
                        <td>Master Ali</td>
                        <td><span class="badge bg-info text-dark">Polishing</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline-secondary">Details</a></td>
                    </tr>
                    <tr>
                        <td><strong>#FF-B-103</strong></td>
                        <td>Emerald Floral Pendant</td>
                        <td>18K Yellow Gold (6.1g)</td>
                        <td>1x Natural Emerald (2.4ct)</td>
                        <td>Master Ahamed</td>
                        <td><span class="badge bg-success">Quality Check</span></td>
                        <td><a href="#" class="btn btn-sm btn-outline-secondary">Details</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
