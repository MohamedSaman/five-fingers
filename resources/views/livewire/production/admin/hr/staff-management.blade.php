@php
    $rs = fn ($v, $d = 0) => number_format((float) $v, $d);
    $deptColors = [
        'Design & 3D' => 'blue',
        'Casting & Foundry' => 'amber',
        'Bench & Setting' => 'gold',
        'Finishing & Luster' => 'violet',
        'Quality Control' => 'green',
        'Assembly' => '',
    ];
@endphp

<div class="page">
    <div class="lw-loading" wire:loading.delay></div>

    <!-- Header -->
    <header class="page-head">
        <div>
            <div class="eyebrow"><i class="bi bi-people"></i> HR & Personnel</div>
            <h1 class="page-title">Artisans & Workshop Staff</h1>
            <p class="page-sub">Master goldsmiths, CAD designers, casters, setters, polishers, and bench craftsmen.</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-gold" wire:click="openCreateModal">
                <i class="bi bi-person-plus"></i> Add New Artisan
            </button>
        </div>
    </header>

    <!-- KPIs -->
    <section class="kpis" aria-label="Staff KPIs">
        <article class="card kpi" style="--kpi-c: var(--gold); --kpi-glow: rgba(217,180,90,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Total Artisans</span>
                <span class="kpi-icon"><i class="bi bi-people"></i></span>
            </div>
            <div class="kpi-value gold-text">{{ $totalCount }}</div>
            <div class="kpi-foot">Craftsmen in workshop</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--green); --kpi-glow: rgba(94,230,154,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Active on Bench</span>
                <span class="kpi-icon"><i class="bi bi-check2-circle"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--green)">{{ $activeCount }}</div>
            <div class="kpi-foot">{{ $totalCount > 0 ? round(($activeCount / $totalCount) * 100) : 0 }}% active roster</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--violet); --kpi-glow: rgba(196,181,253,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Base Monthly Payroll</span>
                <span class="kpi-icon"><i class="bi bi-cash-stack"></i></span>
            </div>
            <div class="kpi-value"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalPayroll) }}</div>
            <div class="kpi-foot">Total baseline salaries</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--blue); --kpi-glow: rgba(147,197,253,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Specialist Divisions</span>
                <span class="kpi-icon"><i class="bi bi-diagram-3"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--blue)">{{ $departmentsCount }}</div>
            <div class="kpi-foot">Design, casting, bench, polish</div>
        </article>
    </section>

    <!-- Artisans Table Section -->
    <section class="card">
        <div class="toolbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" class="input" placeholder="Search by name, role, email, NIC…" wire:model.live.debounce.300ms="search" aria-label="Search artisans">
            </div>

            <select class="select" wire:model.live="departmentFilter" style="width:auto;min-width:180px" aria-label="Filter by division">
                <option value="">All Divisions</option>
                @foreach ($departments as $dept)
                    <option value="{{ $dept }}">{{ $dept }}</option>
                @endforeach
            </select>

            <div class="filter-tabs" role="tablist" aria-label="Status filter">
                <button type="button" class="{{ $statusFilter === '' ? 'is-active' : '' }}" wire:click="$set('statusFilter', '')">All</button>
                <button type="button" class="{{ $statusFilter === 'active' ? 'is-active' : '' }}" wire:click="$set('statusFilter', 'active')">Active</button>
                <button type="button" class="{{ $statusFilter === 'inactive' ? 'is-active' : '' }}" wire:click="$set('statusFilter', 'inactive')">Inactive</button>
            </div>
        </div>

        <div class="table-wrap" tabindex="0">
            <table class="tbl">
                <thead>
                    <tr>
                        <th scope="col" style="min-width:280px">Artisan / Craftsman</th>
                        <th scope="col">Division & Designation</th>
                        <th scope="col">Joined</th>
                        <th scope="col" class="r">Basic Salary</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="r">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staffMembers as $staff)
                        @php
                            $detail = $staff->userDetail;
                            $dept = $detail?->department ?? 'Bench & Setting';
                            $role = $detail?->work_role ?? 'Artisan';
                            $deptCol = $deptColors[$dept] ?? '';
                            $isActive = ($detail?->status ?? 'active') === 'active';
                        @endphp
                        <tr wire:key="staff-{{ $staff->id }}">
                            <th scope="row">
                                <div class="job">
                                    <div class="job-thumb gold" style="font-size:18px">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <div style="min-width:0">
                                        <div class="job-title">{{ $staff->name }}</div>
                                        <div class="job-meta">
                                            <span>{{ $staff->email }}</span>
                                            <span>·</span>
                                            <span>NIC: {{ $detail?->nic_num ?: '—' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:4px">
                                    <span class="chip {{ $deptCol }}" style="height:20px;font-size:10.5px;align-self:flex-start">{{ $dept }}</span>
                                    <span style="font-size:13px;font-weight:600;color:var(--text)">{{ $role }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="mono" style="font-size:12.5px;color:var(--text-2)">
                                    {{ $detail?->join_date ? $detail->join_date->format('d M Y') : '—' }}
                                </span>
                            </td>
                            <td class="r">
                                <div class="money gold" style="font-size:14px">
                                    <span class="cur">Rs</span>{{ $rs($detail?->basic_salary ?? 0) }}
                                </div>
                                <span style="font-size:11px;color:var(--text-3)">/ month</span>
                            </td>
                            <td>
                                <button type="button" class="chip {{ $isActive ? 'green' : '' }}" wire:click="toggleStatus({{ $staff->id }})" title="Click to toggle status" style="cursor:pointer;border:none">
                                    <span class="dot"></span>{{ $isActive ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="r">
                                <div style="display:inline-flex;gap:6px">
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="viewStaff({{ $staff->id }})" title="View Dossier">
                                        <i class="bi bi-eye"></i> Dossier
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="editStaff({{ $staff->id }})" title="Edit Details">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="deleteStaff({{ $staff->id }})" wire:confirm="Are you sure you want to remove this staff member?" title="Remove" style="color:var(--rose)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:48px 20px;color:var(--text-3)">
                                <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px;opacity:.5"></i>
                                No artisans or staff members match your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div style="margin-top:16px">
        {{ $staffMembers->links() }}
    </div>

    <!-- ================= CREATE / EDIT MODAL ================= -->
    @if ($showCreateModal || $showEditModal)
        <div class="dlg-backdrop"></div>
        <dialog class="dlg" open style="max-width:680px">
            <header class="dlg-head">
                <div>
                    <span class="dlg-kicker">{{ $showCreateModal ? 'ATELIER ROSTER' : 'UPDATE RECORD' }}</span>
                    <h2 class="dlg-title">{{ $showCreateModal ? 'Add New Artisan / Staff' : 'Edit Artisan Profile' }}</h2>
                </div>
                <button type="button" class="dlg-close" wire:click="closeModal" aria-label="Close dialog"><i class="bi bi-x-lg"></i></button>
            </header>

            <form wire:submit.prevent="{{ $showCreateModal ? 'saveStaff' : 'updateStaff' }}">
                <div class="dlg-body" style="gap:16px">
                    <div class="grid-2">
                        <div class="field">
                            <label for="f-name">Full Name *</label>
                            <input id="f-name" class="input" wire:model="name" placeholder="e.g. S. Nilam" required autofocus>
                            @error('name') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="f-email">Email Address *</label>
                            <input id="f-email" type="email" class="input" wire:model="email" placeholder="e.g. nilam@fivefingers.com" required>
                            @error('email') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    @if ($showCreateModal)
                        <div class="field">
                            <label for="f-password">System Password * <span style="color:var(--text-dim);font-weight:400">(Min. 6 chars for login)</span></label>
                            <input id="f-password" type="password" class="input" wire:model="password" required>
                            @error('password') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="field">
                            <label for="f-password-edit">Change Password <span style="color:var(--text-dim);font-weight:400">(Leave blank to keep existing)</span></label>
                            <input id="f-password-edit" type="password" class="input" wire:model="password" placeholder="••••••••">
                            @error('password') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-dept">Department / Division *</label>
                            <select id="f-dept" class="select" wire:model="department" required>
                                <option value="Design & 3D">Design & 3D CAD</option>
                                <option value="Casting & Foundry">Casting & Foundry</option>
                                <option value="Bench & Setting">Bench & Stone Setting</option>
                                <option value="Finishing & Luster">Finishing & Luster Polish</option>
                                <option value="Quality Control">Quality Control & Vault</option>
                                <option value="Assembly">Assembly & Goldsmithing</option>
                            </select>
                            @error('department') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="f-role">Work Role / Designation *</label>
                            <input id="f-role" class="input" wire:model="work_role" placeholder="e.g. Master Stone Setter" required>
                            @error('work_role') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid-3">
                        <div class="field">
                            <label for="f-salary">Basic Salary (Monthly) *</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="f-salary" type="number" step="100" min="0" class="input" wire:model="basic_salary" required>
                            </div>
                            @error('basic_salary') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="f-gender">Gender</label>
                            <select id="f-gender" class="select" wire:model="gender">
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="f-status">Employment Status</label>
                            <select id="f-status" class="select" wire:model="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-nic">National Identity Card (NIC)</label>
                            <input id="f-nic" class="input" wire:model="nic_num" placeholder="e.g. 199049182301">
                        </div>
                        <div class="field">
                            <label for="f-join">Joining Date</label>
                            <input id="f-join" type="date" class="input" wire:model="join_date">
                        </div>
                    </div>

                    <div class="field">
                        <label for="f-address">Atelier / Residential Address</label>
                        <input id="f-address" class="input" wire:model="address" placeholder="e.g. Colombo Gold Souk Atelier, Sea Street">
                    </div>

                    <div class="field">
                        <label for="f-desc">Artisan Bio & Specializations</label>
                        <textarea id="f-desc" class="textarea" rows="2" wire:model="description" placeholder="Notes on bench certifications, diamond setting proficiencies, casting alloy experience…"></textarea>
                    </div>
                </div>

                <div class="dlg-foot" style="justify-content:flex-end;gap:8px">
                    <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-gold">
                        <i class="bi bi-check-lg"></i> {{ $showCreateModal ? 'Create Artisan' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </dialog>
    @endif

    <!-- ================= VIEW DOSSIER MODAL ================= -->
    @if ($showViewModal && $selectedStaff)
        @php
            $d = $selectedStaff->userDetail;
        @endphp
        <div class="dlg-backdrop"></div>
        <dialog class="dlg" open style="max-width:720px">
            <header class="dlg-head">
                <div style="display:flex;align-items:center;gap:14px">
                    <div class="job-thumb gold" style="width:50px;height:50px;font-size:22px">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <span class="dlg-kicker">CRAFTSMAN DOSSIER</span>
                        <h2 class="dlg-title">{{ $selectedStaff->name }}</h2>
                        <div style="font-size:12px;color:var(--text-dim);margin-top:2px">{{ $selectedStaff->email }}</div>
                    </div>
                </div>
                <button type="button" class="dlg-close" wire:click="closeModal" aria-label="Close dialog"><i class="bi bi-x-lg"></i></button>
            </header>

            <div class="dlg-body" style="gap:16px">
                <!-- Overview Tiles -->
                <div class="tiles" style="grid-template-columns:repeat(3, minmax(0, 1fr))">
                    <div class="tile">
                        <span class="label">Division</span>
                        <div class="val" style="font-size:14px">{{ $d?->department ?? 'General' }}</div>
                    </div>
                    <div class="tile">
                        <span class="label">Work Role</span>
                        <div class="val" style="font-size:14px">{{ $d?->work_role ?? 'Artisan' }}</div>
                    </div>
                    <div class="tile">
                        <span class="label">Basic Remuneration</span>
                        <div class="val gold-text tnum">Rs {{ $rs($d?->basic_salary ?? 0) }}/mo</div>
                    </div>
                    <div class="tile">
                        <span class="label">Employment Status</span>
                        <div class="val"><span class="chip {{ ($d?->status ?? 'active') === 'active' ? 'green' : '' }}"><span class="dot"></span>{{ ucfirst($d?->status ?? 'active') }}</span></div>
                    </div>
                    <div class="tile">
                        <span class="label">Joined Date</span>
                        <div class="val tnum">{{ $d?->join_date ? $d->join_date->format('d M Y') : '—' }}</div>
                    </div>
                    <div class="tile">
                        <span class="label">NIC Identification</span>
                        <div class="val mono" style="font-size:12.5px">{{ $d?->nic_num ?: '—' }}</div>
                    </div>
                </div>

                @if ($d?->description)
                    <div class="block" style="background:var(--panel-2);padding:12px 14px;border-radius:var(--r-md);border:1px solid var(--line)">
                        <div class="section-label" style="margin:0 0 4px 0">Craftsman Notes</div>
                        <p style="font-size:12.5px;color:var(--text);margin:0;line-height:1.5">{{ $d->description }}</p>
                    </div>
                @endif

                <!-- Recent Assigned Production Stages -->
                <div class="block">
                    <div class="section-label">Recent Atelier Stage Work ({{ $selectedStaffStages->count() }})</div>
                    @if ($selectedStaffStages->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:6px">
                            @foreach ($selectedStaffStages as $stg)
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--panel-2);border:1px solid var(--line);border-radius:var(--r-sm);font-size:12.5px">
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <span class="chip gold" style="height:18px;font-size:10px">Step {{ $stg->step_number }}</span>
                                        <span style="font-weight:600">{{ $stg->step_name }}</span>
                                        <span style="color:var(--text-dim)">· {{ $stg->product?->sku ?? '' }} ({{ $stg->product?->title ?? '' }})</span>
                                    </div>
                                    <span class="chip {{ $stg->status === 'completed' ? 'green' : ($stg->status === 'in_progress' ? 'amber' : '') }}" style="height:18px;font-size:10px">
                                        {{ ucfirst(str_replace('_', ' ', $stg->status)) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size:12px;color:var(--text-dim);font-style:italic">No production stages currently assigned.</div>
                    @endif
                </div>

                <!-- Recent Attendance Logs -->
                <div class="block">
                    <div class="section-label">Recent Attendance Logs ({{ $selectedStaffAttendances->count() }})</div>
                    @if ($selectedStaffAttendances->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:6px">
                            @foreach ($selectedStaffAttendances as $att)
                                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--panel-2);border:1px solid var(--line);border-radius:var(--r-sm);font-size:12px">
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <span class="mono">{{ $att->date }}</span>
                                        <span>· In: <b>{{ $att->check_in ?? '—' }}</b></span>
                                        <span>· Out: <b>{{ $att->check_out ?? '—' }}</b></span>
                                        <span class="tnum" style="color:var(--gold)">({{ $att->time_worked ?? 0 }} hrs)</span>
                                    </div>
                                    <span class="chip {{ $att->status === 'present' ? 'green' : 'red' }}" style="height:18px;font-size:10px">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="font-size:12px;color:var(--text-dim);font-style:italic">No attendance records logged yet.</div>
                    @endif
                </div>
            </div>

            <div class="dlg-foot" style="justify-content:flex-end">
                <button type="button" class="btn btn-ghost" wire:click="closeModal">Close</button>
            </div>
        </dialog>
    @endif
</div>
