<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1" style="color: #d4af37;">Production Batches</h3>
            <p class="text-muted small m-0">Track jewellery batches from casting to final hallmarking and vault storage</p>
        </div>
        <button class="btn btn-warning text-dark fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Create Batch
        </button>
    </div>

    <div class="card border-0 shadow-sm p-4">
        <div class="d-flex justify-content-between mb-3">
            <input type="text" class="form-control w-25" placeholder="Search batch by code or artisan...">
            <div class="d-flex gap-2">
                <select class="form-select w-auto">
                    <option value="">All Metal Types</option>
                    <option value="22k">22K Gold</option>
                    <option value="18k">18K Gold</option>
                    <option value="platinum">Platinum</option>
                </select>
                <select class="form-select w-auto">
                    <option value="">All Stages</option>
                    <option value="casting">Casting</option>
                    <option value="filing">Filing & Mounting</option>
                    <option value="setting">Gemstone Setting</option>
                    <option value="polishing">Polishing</option>
                    <option value="hallmarking">Hallmarking</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <th>Batch #</th>
                        <th>Created Date</th>
                        <th>Design Item</th>
                        <th>Metal Weight (g)</th>
                        <th>Estimated Loss</th>
                        <th>Assigned Goldsmith</th>
                        <th>Current Stage</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>#FF-B-101</strong></td>
                        <td>Oct 20, 2026</td>
                        <td>Ceylon Sapphire Solitaire</td>
                        <td>4.2 g</td>
                        <td>0.12 g (2.8%)</td>
                        <td>Niflan</td>
                        <td><span class="badge bg-warning text-dark">Stone Setting</span></td>
                        <td><button class="btn btn-sm btn-outline-primary">Manage</button></td>
                    </tr>
                    <tr>
                        <td><strong>#FF-B-102</strong></td>
                        <td>Oct 19, 2026</td>
                        <td>Custom Eternity Band</td>
                        <td>6.5 g</td>
                        <td>0.18 g (2.7%)</td>
                        <td>Ali</td>
                        <td><span class="badge bg-info text-dark">Polishing</span></td>
                        <td><button class="btn btn-sm btn-outline-primary">Manage</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
