@php
    $rs = fn ($v, $d = 0) => number_format((float) $v, $d);
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
@endphp

<div class="page">
    <div class="lw-loading" wire:loading.delay></div>

    <!-- Header -->
    <header class="page-head">
        <div>
            <div class="eyebrow"><i class="bi bi-cash-stack"></i> HR & Payroll</div>
            <h1 class="page-title">Salary & Artisan Compensation</h1>
            <p class="page-sub">Monthly remuneration, bench overtime, piece-rate stage bonuses, and disbursements.</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-gold" wire:click="generateMonthlyPayroll">
                <i class="bi bi-calculator"></i> Run Payroll for {{ $months[$selectedMonth] }} {{ $selectedYear }}
            </button>
        </div>
    </header>

    <!-- KPIs -->
    <section class="kpis" aria-label="Salary KPIs">
        <article class="card kpi" style="--kpi-c: var(--gold); --kpi-glow: rgba(217,180,90,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Total Monthly Payroll</span>
                <span class="kpi-icon"><i class="bi bi-wallet2"></i></span>
            </div>
            <div class="kpi-value gold-text"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalPayroll) }}</div>
            <div class="kpi-foot">Net payable for {{ $months[$selectedMonth] }}</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--green); --kpi-glow: rgba(94,230,154,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Disbursed / Paid</span>
                <span class="kpi-icon"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--green)"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalPaid) }}</div>
            <div class="kpi-foot">Paid out through bank / cash</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--amber); --kpi-glow: rgba(251,191,36,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Pending Approval</span>
                <span class="kpi-icon"><i class="bi bi-hourglass-split"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--amber)"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalPending) }}</div>
            <div class="kpi-foot">Awaiting final disbursement</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--violet); --kpi-glow: rgba(196,181,253,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Overtime & Bonuses</span>
                <span class="kpi-icon"><i class="bi bi-stars"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--violet)"><small style="margin:0 4px 0 0">Rs</small>{{ $rs($totalOvertime + $totalBonus) }}</div>
            <div class="kpi-foot">Performance & bench hours</div>
        </article>
    </section>

    <!-- Payroll Table Section -->
    <section class="card">
        <div class="toolbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" class="input" placeholder="Search artisan by name…" wire:model.live.debounce.300ms="search" aria-label="Search payroll">
            </div>

            <select class="select" wire:model.live="selectedMonth" style="width:auto;min-width:140px" aria-label="Select month">
                @foreach ($months as $num => $m)
                    <option value="{{ $num }}">{{ $m }}</option>
                @endforeach
            </select>

            <select class="select" wire:model.live="selectedYear" style="width:auto;min-width:100px" aria-label="Select year">
                @foreach ([2024, 2025, 2026, 2027] as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>

            <div class="filter-tabs" role="tablist" aria-label="Payment status filter">
                <button type="button" class="{{ $statusFilter === '' ? 'is-active' : '' }}" wire:click="$set('statusFilter', '')">All</button>
                <button type="button" class="{{ $statusFilter === 'paid' ? 'is-active' : '' }}" wire:click="$set('statusFilter', 'paid')">Paid</button>
                <button type="button" class="{{ $statusFilter === 'pending' ? 'is-active' : '' }}" wire:click="$set('statusFilter', 'pending')">Pending</button>
            </div>
        </div>

        <div class="table-wrap" tabindex="0">
            <table class="tbl">
                <thead>
                    <tr>
                        <th scope="col" style="min-width:260px">Artisan</th>
                        <th scope="col" class="r">Basic Salary</th>
                        <th scope="col" class="r">Bonus & Allow.</th>
                        <th scope="col" class="r">Overtime</th>
                        <th scope="col" class="r">Deductions</th>
                        <th scope="col" class="r">Net Salary</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="r">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($salaries as $sal)
                        @php
                            $user = $sal->user;
                            $detail = $user?->userDetail;
                            $isPaid = $sal->payment_status === 'paid';
                        @endphp
                        <tr wire:key="sal-{{ $sal->salary_id }}">
                            <th scope="row">
                                <div class="job">
                                    <div class="job-thumb gold" style="font-size:18px">
                                        <i class="bi bi-wallet2"></i>
                                    </div>
                                    <div style="min-width:0">
                                        <div class="job-title">{{ $user?->name ?? 'Artisan' }}</div>
                                        <div class="job-meta">
                                            <span>{{ $detail?->work_role ?? 'Artisan' }}</span>
                                            <span>·</span>
                                            <span>{{ $detail?->department ?? 'Atelier' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <td class="r">
                                <span class="tnum" style="font-size:13px;color:var(--text)">Rs {{ $rs($sal->basic_salary) }}</span>
                            </td>
                            <td class="r">
                                <span class="tnum" style="font-size:12.5px;color:var(--green)">
                                    +Rs {{ $rs($sal->bonus + $sal->allowance) }}
                                </span>
                            </td>
                            <td class="r">
                                <div class="tnum" style="font-size:12.5px;color:var(--violet)">
                                    +Rs {{ $rs($sal->overtime) }}
                                </div>
                                @if ($sal->overtime_hours > 0)
                                    <div style="font-size:10.5px;color:var(--text-3)">{{ $sal->overtime_hours }} hrs</div>
                                @endif
                            </td>
                            <td class="r">
                                <span class="tnum" style="font-size:12.5px;color:var(--rose)">
                                    -Rs {{ $rs($sal->deductions) }}
                                </span>
                            </td>
                            <td class="r">
                                <div class="money gold" style="font-size:14.5px">
                                    <span class="cur">Rs</span>{{ $rs($sal->net_salary) }}
                                </div>
                            </td>
                            <td>
                                <button type="button" class="chip {{ $isPaid ? 'green' : 'amber' }}" wire:click="togglePaymentStatus({{ $sal->salary_id }})" title="Click to toggle status" style="cursor:pointer;border:none">
                                    <span class="dot"></span>{{ $isPaid ? 'Paid' : 'Pending' }}
                                </button>
                            </td>
                            <td class="r">
                                <div style="display:inline-flex;gap:6px">
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="viewPayslip({{ $sal->salary_id }})" title="View Official Payslip">
                                        <i class="bi bi-file-earmark-text"></i> Payslip
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="editSalary({{ $sal->salary_id }})" title="Adjust Salary / Bonuses">
                                        <i class="bi bi-sliders"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="deleteSalary({{ $sal->salary_id }})" wire:confirm="Delete this payroll record?" title="Delete" style="color:var(--rose)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:48px 20px;color:var(--text-3)">
                                <i class="bi bi-cash-stack" style="font-size:32px;display:block;margin-bottom:8px;opacity:.5"></i>
                                No salary records found for {{ $months[$selectedMonth] }} {{ $selectedYear }}.
                                <div style="margin-top:12px">
                                    <button type="button" class="btn btn-sm btn-gold" wire:click="generateMonthlyPayroll">
                                        <i class="bi bi-calculator"></i> Run Payroll
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div style="margin-top:16px">
        {{ $salaries->links() }}
    </div>

    <!-- ================= PAYSLIP MODAL ================= -->
    @if ($showPayslipModal && $payslip)
        @php
            $u = $payslip->user;
            $d = $u?->userDetail;
            $mName = $payslip->salary_month ? $payslip->salary_month->format('F Y') : 'Current Period';
        @endphp
        <div class="dlg-backdrop" wire:click="closeModal"></div>
        <div class="dlg" role="dialog" aria-modal="true" style="max-width:680px" @keydown.escape.window="$wire.closeModal()">
            <header class="dlg-head" style="border-bottom:1px solid var(--gold-line)">
                <div style="display:flex;align-items:center;gap:12px">
                    <img src="{{ asset('images/five-fingers-logo.png') }}" style="height:36px;width:auto" alt="Logo">
                    <div>
                        <span class="dlg-kicker">FIVE FINGERS ATELIER</span>
                        <h2 class="dlg-title" style="font-size:18px">Official Remuneration Advice · {{ $mName }}</h2>
                    </div>
                </div>
                <button type="button" class="dlg-close" wire:click="closeModal" aria-label="Close dialog"><i class="bi bi-x-lg"></i></button>
            </header>

            <div class="dlg-body" style="gap:16px" id="printable-payslip">
                <!-- Artisan Info Bar -->
                <div style="display:flex;justify-content:space-between;padding:12px 16px;background:var(--panel-2);border:1px solid var(--line);border-radius:var(--r-md);font-size:12.5px">
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--text)">{{ $u?->name }}</div>
                        <div style="color:var(--text-dim);margin-top:2px">{{ $d?->work_role ?? 'Artisan' }} · {{ $d?->department ?? 'Atelier' }}</div>
                    </div>
                    <div style="text-align:right">
                        <span class="chip {{ $payslip->payment_status === 'paid' ? 'green' : 'amber' }}">
                            <span class="dot"></span>{{ strtoupper($payslip->payment_status) }}
                        </span>
                        <div style="font-size:11px;color:var(--text-dim);margin-top:4px">Ref: FF-PAY-{{ $payslip->salary_id }}</div>
                    </div>
                </div>

                <!-- Earnings & Deductions Breakdown -->
                <div class="grid-2" style="gap:16px">
                    <!-- Earnings -->
                    <div style="border:1px solid var(--line);border-radius:var(--r-md);overflow:hidden">
                        <div style="padding:8px 12px;background:var(--panel-2);font-weight:700;font-size:12px;color:var(--text);border-bottom:1px solid var(--line)">
                            <i class="bi bi-plus-circle" style="color:var(--green)"></i> EARNINGS & INCENTIVES
                        </div>
                        <div style="padding:10px 12px;display:flex;flex-direction:column;gap:8px;font-size:12.5px">
                            <div style="display:flex;justify-content:space-between">
                                <span>Basic Salary</span>
                                <b class="tnum">Rs {{ $rs($payslip->basic_salary) }}</b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span>Stage Labor Bonus</span>
                                <b class="tnum" style="color:var(--green)">+Rs {{ $rs($payslip->bonus) }}</b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span>Bench Allowance</span>
                                <b class="tnum" style="color:var(--green)">+Rs {{ $rs($payslip->allowance) }}</b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span>Overtime Pay ({{ $payslip->overtime_hours ?? 0 }}h)</span>
                                <b class="tnum" style="color:var(--violet)">+Rs {{ $rs($payslip->overtime) }}</b>
                            </div>
                        </div>
                    </div>

                    <!-- Deductions -->
                    <div style="border:1px solid var(--line);border-radius:var(--r-md);overflow:hidden">
                        <div style="padding:8px 12px;background:var(--panel-2);font-weight:700;font-size:12px;color:var(--text);border-bottom:1px solid var(--line)">
                            <i class="bi bi-dash-circle" style="color:var(--rose)"></i> DEDUCTIONS
                        </div>
                        <div style="padding:10px 12px;display:flex;flex-direction:column;gap:8px;font-size:12.5px">
                            <div style="display:flex;justify-content:space-between">
                                <span>Statutory EPF / ETF</span>
                                <b class="tnum" style="color:var(--rose)">-Rs {{ $rs($payslip->deductions) }}</b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span>Staff Advances / Loans</span>
                                <b class="tnum">Rs 0</b>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                <span>Bench Loss Recovery</span>
                                <b class="tnum">Rs 0</b>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Payable Highlight -->
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:rgba(217,180,90,0.08);border:1.5px solid var(--gold-line);border-radius:var(--r-md)">
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;color:var(--gold);text-transform:uppercase">NET DISBURSED AMOUNT</div>
                        <div style="font-size:11.5px;color:var(--text-dim);margin-top:2px">Disbursed via Commercial Bank Atelier Account</div>
                    </div>
                    <div class="money gold" style="font-size:24px;font-weight:800">
                        <span class="cur">Rs</span>{{ $rs($payslip->net_salary) }}
                    </div>
                </div>

                <div style="display:flex;justify-content:space-between;margin-top:14px;padding-top:14px;border-top:1px dashed var(--line);font-size:11px;color:var(--text-dim)">
                    <div>Craftsman Signature: ___________________</div>
                    <div>Managing Director: ___________________</div>
                </div>
            </div>

            <div class="dlg-foot" style="justify-content:flex-end;gap:8px">
                <button type="button" class="btn btn-ghost" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Payslip
                </button>
                <button type="button" class="btn btn-gold" wire:click="closeModal">Done</button>
            </div>
        </div>
    @endif

    <!-- ================= ADJUST SALARY MODAL ================= -->
    @if ($showAdjustModal)
        <div class="dlg-backdrop" wire:click="closeModal"></div>
        <div class="dlg" role="dialog" aria-modal="true" style="max-width:540px" @keydown.escape.window="$wire.closeModal()">
            <header class="dlg-head">
                <div>
                    <span class="dlg-kicker">PAYROLL ADJUSTMENT</span>
                    <h2 class="dlg-title">Adjust Compensation · {{ $artisanName }}</h2>
                </div>
                <button type="button" class="dlg-close" wire:click="closeModal" aria-label="Close dialog"><i class="bi bi-x-lg"></i></button>
            </header>

            <form wire:submit.prevent="saveSalaryAdjustment">
                <div class="dlg-body" style="gap:14px">
                    <div class="field">
                        <label for="f-basic">Basic Salary</label>
                        <div class="input-wrap prefix">
                            <span class="unit">Rs</span>
                            <input id="f-basic" type="number" step="100" min="0" class="input" wire:model="basic_salary" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-bonus">Bonus / Incentive</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="f-bonus" type="number" step="100" min="0" class="input" wire:model="bonus">
                            </div>
                        </div>
                        <div class="field">
                            <label for="f-allow">Allowance</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="f-allow" type="number" step="100" min="0" class="input" wire:model="allowance">
                            </div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-ot">Overtime Remuneration</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="f-ot" type="number" step="100" min="0" class="input" wire:model="overtime">
                            </div>
                        </div>
                        <div class="field">
                            <label for="f-deduct">Deductions</label>
                            <div class="input-wrap prefix">
                                <span class="unit">Rs</span>
                                <input id="f-deduct" type="number" step="100" min="0" class="input" wire:model="deductions">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="f-pstatus">Payment Status</label>
                        <select id="f-pstatus" class="select" wire:model="payment_status">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>

                <div class="dlg-foot" style="justify-content:flex-end;gap:8px">
                    <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-gold">Save Adjustment</button>
                </div>
            </form>
        </div>
    @endif
</div>
