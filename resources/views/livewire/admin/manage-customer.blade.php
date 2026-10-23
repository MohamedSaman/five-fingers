<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h3 class="fw-bold text-dark mb-2">
                <i class="bi bi-people-fill text-success me-2"></i> Manage Customers
            </h3>
            <p class="text-muted mb-0">Manage all customer information efficiently</p>
        </div>
        <div>
            <button class="btn btn-primary" wire:click="createCustomer">
                <i class="bi bi-plus-lg me-2"></i> Create Customer
            </button>
        </div>
    </div>

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Customer List --}}
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-journal-text text-primary me-2"></i> Customer List
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="text-sm text-muted fw-medium">Show</label>
                <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 80px;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="500">500</option>
                </select>
                <span class="text-sm text-muted">entries</span>
            </div>
        </div>
        <div class="card-body p-0 overflow-auto">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Customer Name</th>
                            <th>Business Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Address</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($customers->count() > 0)
                            @foreach ($customers as $customer)
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td>
                                    <span class="fw-medium text-dark">{{ $customer->name ?? '-' }}</span>
                                </td>
                                <td>{{ $customer->business_name ?? '-' }}</td>
                                <td>{{ $customer->phone ?? '-' }}</td>
                                <td>{{ $customer->email ?? '-' }}</td>
                                <td>
                                    @if($customer->type == 'retail')
                                    <span class="badge bg-success">Retail</span>
                                    @elseif($customer->type == 'wholesale')
                                    <span class="badge bg-info">Wholesale</span>
                                    @else
                                    <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $customer->address ?? '-' }}</td>
                                <td class="text-end pe-2">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            <i class="bi bi-gear-fill"></i> Actions
        </button>

        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <!-- View Customer -->
            <li>
                <button class="dropdown-item"
                        wire:click="viewDetails({{ $customer->id }})"
                        wire:loading.attr="disabled"
                        title="View Details">
                    <span wire:loading wire:target="viewDetails({{ $customer->id }})">
                        <i class="spinner-border spinner-border-sm me-2"></i> Loading...
                    </span>
                    <span wire:loading.remove wire:target="viewDetails({{ $customer->id }})">
                        <i class="bi bi-eye text-info me-2"></i> View
                    </span>
                </button>
            </li>

            <!-- Edit Customer -->
            <li>
                <button class="dropdown-item"
                        wire:click="editCustomer({{ $customer->id }})"
                        wire:loading.attr="disabled"
                        title="Edit Customer">
                    <span wire:loading wire:target="editCustomer({{ $customer->id }})">
                        <i class="spinner-border spinner-border-sm me-2"></i> Loading...
                    </span>
                    <span wire:loading.remove wire:target="editCustomer({{ $customer->id }})">
                        <i class="bi bi-pencil text-primary me-2"></i> Edit
                    </span>
                </button>
            </li>

            <!-- Delete Customer -->
            <li>
                <button class="dropdown-item"
                        wire:click="confirmDelete({{ $customer->id }})"
                        wire:loading.attr="disabled"
                        title="Delete Customer">
                    <span wire:loading wire:target="confirmDelete({{ $customer->id }})">
                        <i class="spinner-border spinner-border-sm me-2"></i> Loading...
                    </span>
                    <span wire:loading.remove wire:target="confirmDelete({{ $customer->id }})">
                        <i class="bi bi-trash text-danger me-2"></i> Delete
                    </span>
                </button>
            </li>
        </ul>
    </div>
</td>

                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="bi bi-people display-4 d-block mb-2"></i>
                                    No customers found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-center">
                    {{ $customers->links('livewire.custom-pagination') }}
                </div>
            </div>
        </div>
    </div>

 {{-- Create Customer Modal --}}
@if($showCreateModal)
<div class="modal fade show d-block" tabindex="-1" aria-labelledby="createCustomerModalLabel" aria-hidden="false" style="background-color: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-white me-2"></i> Create Customer
                </h5>
                <button type="button" class="btn-close" wire:click="closeModal"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="saveCustomer">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Customer Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       wire:model="name" placeholder="Enter customer name" required>
                                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Contact Number</label>
                                <input type="text" class="form-control @error('contactNumber') is-invalid @enderror" 
                                       wire:model="contactNumber" placeholder="Enter contact number" >
                                @error('contactNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       wire:model="email" placeholder="Enter email">
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Business Name</label>
                                <input type="text" class="form-control @error('businessName') is-invalid @enderror" 
                                       wire:model="businessName" placeholder="Enter Business Name">
                                @error('businessName') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Customer Type</label>
                                <select class="form-select @error('customerType') is-invalid @enderror" wire:model="customerType" >
                                    <option value="">Select customer type</option>
                                    <option value="retail">Retail</option>
                                    <option value="wholesale">Wholesale</option>
                                </select>
                                @error('customerType') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="mb-1">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       wire:model="address" placeholder="Enter address">
                                @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <i class="bi bi-check2-circle me-1"></i>
                            <span wire:loading.remove>Save Customer</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

    {{-- Edit Customer Modal --}}
    @if($showEditModal)
    <div class="modal fade show d-block" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="false" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square text-white me-2"></i> Edit Customer
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateCustomer">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Customer Name</label>
                                    <input type="text" class="form-control @error('editName') is-invalid @enderror" 
                                           wire:model="editName" required>
                                    @error('editName') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Contact Number</label>
                                    <input type="text" class="form-control @error('editContactNumber') is-invalid @enderror" 
                                           wire:model="editContactNumber" required>
                                    @error('editContactNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control @error('editEmail') is-invalid @enderror" 
                                           wire:model="editEmail">
                                    @error('editEmail') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Business Name</label>
                                    <input type="text" class="form-control @error('editBusinessName') is-invalid @enderror" 
                                           wire:model="editBusinessName">
                                    @error('editBusinessName') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Customer Type</label>
                                    <select class="form-select @error('editCustomerType') is-invalid @enderror" wire:model="editCustomerType" required>
                                        <option value="retail">Retail</option>
                                        <option value="wholesale">Wholesale</option>
                                    </select>
                                    @error('editCustomerType') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Address</label>
                                    <input type="text" class="form-control @error('editAddress') is-invalid @enderror" 
                                           wire:model="editAddress">
                                    @error('editAddress') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <i class="bi bi-check2-circle me-1"></i>
                                <span wire:loading.remove>Update Customer</span>
                                <span wire:loading>Updating...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- View Details Modal --}}
    @if($showViewModal)
    <div class="modal fade show d-block" tabindex="-1" aria-hidden="false" style="background-color:rgba(0,0,0,0.55);">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;">

                {{-- ── Header ── --}}
                <div class="modal-header border-0 pb-0" style="background:linear-gradient(135deg,#2e7d32 0%,#558b2f 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:52px;height:52px;">
                            <i class="bi bi-person-fill text-white fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">{{ $viewCustomerDetail['name'] ?? '-' }}</h5>
                            <small class="text-white text-opacity-75 text-capitalize">
                                {{ $viewCustomerDetail['type'] ?? '' }} Customer
                                @if($viewCustomerDetail['business_name'] ?? false)
                                    · {{ $viewCustomerDetail['business_name'] }}
                                @endif
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" wire:click="closeModal"></button>
                </div>

                {{-- ── Tab Nav ── --}}
                <div class="px-3 pt-0" style="background:linear-gradient(135deg,#2e7d32 0%,#558b2f 100%);">
                    <ul class="nav nav-tabs border-0 gap-1 pt-2">
                        @php
                            $tabs = [
                                'overview' => ['icon'=>'bi-person-lines-fill','label'=>'Overview'],
                                'sales'    => ['icon'=>'bi-cart-fill',        'label'=>'Sales',    'badge'=>count($viewSales)],
                                'payments' => ['icon'=>'bi-credit-card-fill', 'label'=>'Payments', 'badge'=>count($viewPayments)],
                                'dues'     => ['icon'=>'bi-exclamation-triangle-fill','label'=>'Dues','badge'=>count($viewDues),'badgeClass'=>'bg-warning text-dark'],
                                'ledger'   => ['icon'=>'bi-journal-text',     'label'=>'Ledger'],
                            ];
                        @endphp
                        @foreach($tabs as $key => $tab)
                        <li class="nav-item">
                            <button class="nav-link fw-semibold px-3 py-2 border-0 rounded-top-3
                                           {{ $viewTab === $key ? 'bg-white text-success' : 'text-white text-opacity-75 bg-white bg-opacity-10' }}"
                                    wire:click="switchTab('{{ $key }}')">
                                <i class="bi {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
                                @if(isset($tab['badge']) && $tab['badge'] > 0)
                                    <span class="badge ms-1 {{ $tab['badgeClass'] ?? 'bg-success' }}" style="font-size:.7rem;">
                                        {{ $tab['badge'] }}
                                    </span>
                                @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ── Body ── --}}
                <div class="modal-body p-4" style="background:#f8faf8;min-height:420px;">

                    {{-- ════ OVERVIEW TAB ════ --}}
                    @if($viewTab === 'overview')
                    <div class="row g-4">
                        {{-- Personal Info --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger mb-3"><i class="bi bi-person-lines-fill me-2"></i>Personal Information</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted fw-semibold" style="width:38%">Name</td><td class="fw-semibold">{{ $viewCustomerDetail['name'] ?? '-' }}</td></tr>
                                        <tr><td class="text-muted fw-semibold">Contact</td><td>{{ $viewCustomerDetail['phone'] ?? '-' }}</td></tr>
                                        <tr><td class="text-muted fw-semibold">Email</td><td>{{ $viewCustomerDetail['email'] ?? '-' }}</td></tr>
                                        <tr><td class="text-muted fw-semibold">Business</td><td>{{ $viewCustomerDetail['business_name'] ?? '-' }}</td></tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Type</td>
                                            <td>
                                                @if(($viewCustomerDetail['type'] ?? '') === 'wholesale')
                                                    <span class="badge bg-info">Wholesale</span>
                                                @elseif(($viewCustomerDetail['type'] ?? '') === 'retail')
                                                    <span class="badge bg-success">Retail</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Address & Dates --}}
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Address &amp; Dates</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr><td class="text-muted fw-semibold" style="width:38%">Address</td><td>{{ $viewCustomerDetail['address'] ?? '-' }}</td></tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Created</td>
                                            <td>{{ $viewCustomerDetail['created_at'] ? \Carbon\Carbon::parse($viewCustomerDetail['created_at'])->format('M d, Y h:i A') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted fw-semibold">Updated</td>
                                            <td>{{ $viewCustomerDetail['updated_at'] ? \Carbon\Carbon::parse($viewCustomerDetail['updated_at'])->format('M d, Y h:i A') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Summary Cards --}}
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Quick Summary</h6>
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#eef2ff;">
                                                <div class="fw-bold fs-4 text-primary">{{ $viewSummary['total_sales'] ?? 0 }}</div>
                                                <div class="text-muted small">Total Sales</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#ecfdf5;">
                                                <div class="fw-bold fs-4 text-success">{{ $viewSummary['total_payments'] ?? 0 }}</div>
                                                <div class="text-muted small">Total Payments</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#fffbeb;">
                                                <div class="fw-bold fs-4 text-warning">{{ $viewSummary['pending_dues'] ?? 0 }}</div>
                                                <div class="text-muted small">Pending Dues</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#fff0f0;">
                                                <div class="fw-bold fs-5 text-danger">Rs.{{ number_format($viewSummary['total_sales_amount'] ?? 0, 2) }}</div>
                                                <div class="text-muted small">Total Sales Amount</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#ecfdf5;">
                                                <div class="fw-bold fs-5 text-success">Rs.{{ number_format($viewSummary['total_paid'] ?? 0, 2) }}</div>
                                                <div class="text-muted small">Total Paid</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#fffbeb;">
                                                <div class="fw-bold fs-5 text-warning">Rs.{{ number_format($viewSummary['total_due'] ?? 0, 2) }}</div>
                                                <div class="text-muted small">Outstanding Due</div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="rounded-3 p-3 text-center" style="background:#fff0f0;">
                                                <div class="fw-bold fs-5 text-danger">Rs.{{ number_format($viewSummary['total_returns'] ?? 0, 2) }}</div>
                                                <div class="text-muted small">Total Returns</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ════ SALES TAB ════ --}}
                    @if($viewTab === 'sales')
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            @if(count($viewSales) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:#2e7d32;">
                                        <tr>
                                            <th class="text-white ps-3">#</th>
                                            <th class="text-white">Invoice</th>
                                            <th class="text-white">Date</th>
                                            <th class="text-white text-end">Amount</th>
                                            <th class="text-white text-end">Due</th>
                                            <th class="text-white text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($viewSales as $i => $s)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                            <td class="fw-semibold">{{ $s['invoice_number'] }}</td>
                                            <td class="text-muted">{{ $s['date'] }}</td>
                                            <td class="text-end fw-semibold">Rs.{{ number_format($s['total_amount'], 2) }}</td>
                                            <td class="text-end">
                                                @if($s['due_amount'] > 0)
                                                    <span class="text-danger fw-semibold">Rs.{{ number_format($s['due_amount'], 2) }}</span>
                                                @else
                                                    <span class="text-success">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @php $ps = $s['payment_status']; @endphp
                                                <span class="badge {{ $ps === 'paid' ? 'bg-success' : ($ps === 'partial' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                    {{ ucfirst($ps) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x display-4 d-block mb-2"></i>No sales found
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- ════ PAYMENTS TAB ════ --}}
                    @if($viewTab === 'payments')
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            @if(count($viewPayments) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:#2e7d32;">
                                        <tr>
                                            <th class="text-white ps-3">#</th>
                                            <th class="text-white">Invoice</th>
                                            <th class="text-white">Date</th>
                                            <th class="text-white">Method</th>
                                            <th class="text-white text-end">Amount</th>
                                            <th class="text-white text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($viewPayments as $i => $p)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                            <td class="fw-semibold">{{ $p['invoice_number'] ?? '-' }}</td>
                                            <td class="text-muted">{{ $p['date'] }}</td>
                                            <td>
                                                <span class="badge {{ $p['method'] === 'cheque' ? 'bg-info' : ($p['method'] === 'credit' ? 'bg-warning text-dark' : 'bg-success') }}">
                                                    {{ ucfirst($p['method'] ?? '-') }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-semibold text-success">Rs.{{ number_format($p['amount'], 2) }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $p['status'] === 'approved' || $p['status'] === 'paid' ? 'bg-success' : ($p['status'] === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($p['status'] ?? 'pending') }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-credit-card display-4 d-block mb-2"></i>No payments found
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- ════ DUES TAB ════ --}}
                    @if($viewTab === 'dues')
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            @if(count($viewDues) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background:#2e7d32;">
                                        <tr>
                                            <th class="text-white ps-3">#</th>
                                            <th class="text-white">Invoice</th>
                                            <th class="text-white">Date</th>
                                            <th class="text-white text-end">Invoice Total</th>
                                            <th class="text-white text-end">Due Amount</th>
                                            <th class="text-white text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($viewDues as $i => $d)
                                        <tr>
                                            <td class="ps-3 text-muted">{{ $i + 1 }}</td>
                                            <td class="fw-semibold">{{ $d['invoice_number'] }}</td>
                                            <td class="text-muted">{{ $d['date'] }}</td>
                                            <td class="text-end">Rs.{{ number_format($d['total_amount'], 2) }}</td>
                                            <td class="text-end fw-bold text-danger">Rs.{{ number_format($d['due_amount'], 2) }}</td>
                                            <td class="text-center">
                                                @php $ps = $d['payment_status']; @endphp
                                                <span class="badge {{ $ps === 'partial' ? 'bg-warning text-dark' : 'bg-danger' }}">
                                                    {{ ucfirst($ps) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Total Outstanding Due:</td>
                                            <td class="text-end fw-bold text-danger">Rs.{{ number_format(array_sum(array_column($viewDues, 'due_amount')), 2) }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-check-circle display-4 d-block mb-2 text-success"></i>No outstanding dues!
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- ════ LEDGER TAB ════ --}}
                    @if($viewTab === 'ledger')
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            @if(count($viewLedger) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:.9rem;">
                                    <thead style="background:#2e7d32;">
                                        <tr>
                                            <th class="text-white ps-3">Date</th>
                                            <th class="text-white">Description</th>
                                            <th class="text-white text-end">Debit (Rs.)</th>
                                            <th class="text-white text-end">Credit (Rs.)</th>
                                            <th class="text-white text-end pe-3">Balance (Rs.)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($viewLedger as $entry)
                                        <tr class="{{ $entry['type'] === 'sale' ? '' : 'table-success bg-opacity-25' }}">
                                            <td class="ps-3 text-muted text-nowrap">{{ $entry['date'] }}</td>
                                            <td>
                                                @if($entry['type'] === 'sale')
                                                    <i class="bi bi-cart me-1 text-primary"></i>
                                                @elseif($entry['type'] === 'payment')
                                                    <i class="bi bi-credit-card me-1 text-success"></i>
                                                @else
                                                    <i class="bi bi-arrow-return-left me-1 text-warning"></i>
                                                @endif
                                                {{ $entry['description'] }}
                                            </td>
                                            <td class="text-end text-danger fw-semibold">
                                                {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}
                                            </td>
                                            <td class="text-end text-success fw-semibold">
                                                {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}
                                            </td>
                                            <td class="text-end pe-3 fw-bold {{ $entry['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ number_format($entry['balance'], 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="2" class="text-end fw-bold ps-3">Closing Balance:</td>
                                            <td colspan="3" class="text-end pe-3 fw-bold fs-6
                                                {{ ($viewLedger[count($viewLedger)-1]['balance'] ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                                Rs.{{ number_format($viewLedger[count($viewLedger)-1]['balance'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x display-4 d-block mb-2"></i>No ledger entries found
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                </div>{{-- /modal-body --}}

                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="false" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                    </h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-person-x text-danger fs-1 mb-3 d-block"></i>
                    <h5 class="fw-bold mb-3">Are you sure?</h5>
                    <p class="text-muted">You are about to delete this customer. This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteCustomer" wire:loading.attr="disabled">
                        <i class="bi bi-trash me-1"></i>
                        <span wire:loading.remove>Delete Customer</span>
                        <span wire:loading>Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 12px 12px 0 0 !important;
        padding: 1.25rem 1.5rem;
    }
    .table td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }

    .btn-link {
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-link:hover {
        transform: scale(1.1);
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus,
    .form-select:focus {
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        border-color: #4361ee;
    }

    .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .btn-primary:hover {
        background-color: #3f37c9;
        border-color: #3f37c9;
        transform: translateY(-2px);
    }

    .btn-danger {
        background-color: #e63946;
        border-color: #e63946;
    }

    .btn-danger:hover {
        background-color: #d00000;
        border-color: #d00000;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 8px;
        border: none;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
    }

    /* Additional styles for view modal */
    .bg-light {
        background-color: #f8f9fa !important;
    }

    .rounded-start {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }

    .text-capitalize {
        text-transform: capitalize;
    }

    .bg-primary {
        background-color: #4361ee !important;
    }

    .border-bottom {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .pb-3 {
        padding-bottom: 1rem !important;
    }

    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
</style>
@endpush