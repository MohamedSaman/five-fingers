<div class="page">
    <div class="lw-loading" wire:loading.delay></div>

    <!-- Header -->
    <header class="page-head">
        <div>
            <div class="eyebrow"><i class="bi bi-calendar-check"></i> HR & Timekeeping</div>
            <h1 class="page-title">Artisan Attendance & Bench Log</h1>
            <p class="page-sub">Daily workshop attendance, clock-in/out records, worked bench hours, and overtime.</p>
        </div>
        <div class="page-actions">
            <button type="button" class="btn btn-gold" wire:click="openManualAttendanceModal">
                <i class="bi bi-clock-history"></i> Log Attendance Entry
            </button>
        </div>
    </header>

    <!-- KPIs -->
    <section class="kpis" aria-label="Attendance KPIs">
        <article class="card kpi" style="--kpi-c: var(--green); --kpi-glow: rgba(94,230,154,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Present Today</span>
                <span class="kpi-icon"><i class="bi bi-person-check"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--green)">
                {{ $presentTodayCount }} <span style="font-size:16px;color:var(--text-3);font-weight:500">/ {{ $allStaffCount }}</span>
            </div>
            <div class="kpi-foot">{{ $allStaffCount > 0 ? round(($presentTodayCount / $allStaffCount) * 100) : 0 }}% workshop presence</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--blue); --kpi-glow: rgba(147,197,253,.15)">
            <div class="kpi-top">
                <span class="kpi-label">On-Time Arrival</span>
                <span class="kpi-icon"><i class="bi bi-alarm"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--blue)">
                {{ $presentTodayCount > 0 ? round(($onTimeCount / $presentTodayCount) * 100) : 0 }}%
            </div>
            <div class="kpi-foot">{{ $onTimeCount }} craftsmen on schedule</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--gold); --kpi-glow: rgba(217,180,90,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Total Bench Hours Today</span>
                <span class="kpi-icon"><i class="bi bi-stopwatch"></i></span>
            </div>
            <div class="kpi-value gold-text">{{ number_format($totalHoursToday, 1) }} <small style="font-size:14px;margin-left:2px">hrs</small></div>
            <div class="kpi-foot">Cumulative artisan time</div>
        </article>

        <article class="card kpi" style="--kpi-c: var(--violet); --kpi-glow: rgba(196,181,253,.15)">
            <div class="kpi-top">
                <span class="kpi-label">Overtime Hours</span>
                <span class="kpi-icon"><i class="bi bi-lightning-charge"></i></span>
            </div>
            <div class="kpi-value" style="color:var(--violet)">+{{ number_format($overtimeHoursToday, 1) }} <small style="font-size:14px;margin-left:2px">hrs</small></div>
            <div class="kpi-foot">Extended bench fabrication</div>
        </article>
    </section>

    <!-- Attendance Table Section -->
    <section class="card">
        <div class="toolbar">
            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" class="input" placeholder="Search artisan by name…" wire:model.live.debounce.300ms="search" aria-label="Search attendance">
            </div>

            <div class="filter-tabs" role="tablist" aria-label="Date filter tabs">
                <button type="button" class="{{ $dateFilter === 'today' ? 'is-active' : '' }}" wire:click="$set('dateFilter', 'today')">Today</button>
                <button type="button" class="{{ $dateFilter === 'yesterday' ? 'is-active' : '' }}" wire:click="$set('dateFilter', 'yesterday')">Yesterday</button>
                <button type="button" class="{{ $dateFilter === 'this_week' ? 'is-active' : '' }}" wire:click="$set('dateFilter', 'this_week')">This Week</button>
                <button type="button" class="{{ $dateFilter === 'this_month' ? 'is-active' : '' }}" wire:click="$set('dateFilter', 'this_month')">This Month</button>
                <button type="button" class="{{ $dateFilter === 'custom' ? 'is-active' : '' }}" wire:click="$set('dateFilter', 'custom')">Custom</button>
            </div>

            @if ($dateFilter === 'custom')
                <input type="date" class="input" wire:model.live="customDate" style="width:150px" aria-label="Pick date">
            @endif

            <select class="select" wire:model.live="statusFilter" style="width:auto;min-width:130px" aria-label="Status filter">
                <option value="">All Statuses</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
            </select>
        </div>

        <div class="table-wrap" tabindex="0">
            <table class="tbl">
                <thead>
                    <tr>
                        <th scope="col" style="min-width:260px">Artisan</th>
                        <th scope="col">Date</th>
                        <th scope="col">Check In</th>
                        <th scope="col">Check Out</th>
                        <th scope="col" class="r">Hours Worked</th>
                        <th scope="col" class="r">Overtime</th>
                        <th scope="col">Status</th>
                        <th scope="col">Workstation / Notes</th>
                        <th scope="col" class="r">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $att)
                        @php
                            $user = $att->user;
                            $detail = $user?->userDetail;
                            $isPresent = $att->status === 'present';
                            $isOnTime = $att->present_status === 'ontime';
                        @endphp
                        <tr wire:key="att-{{ $att->attendance_id }}">
                            <th scope="row">
                                <div class="job">
                                    <div class="job-thumb gold" style="font-size:18px">
                                        <i class="bi bi-clock-history"></i>
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
                            <td>
                                <span class="mono" style="font-size:12.5px;color:var(--text-2)">
                                    {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="mono" style="font-size:12.5px;color:{{ $att->check_in ? 'var(--text)' : 'var(--text-3)' }}">
                                    {{ $att->check_in ? substr($att->check_in, 0, 5) : '—' }}
                                </span>
                                @if ($att->check_in && $isOnTime)
                                    <span class="chip green" style="height:18px;font-size:10px;padding:0 6px;margin-left:4px">On time</span>
                                @elseif($att->check_in && $att->present_status === 'late')
                                    <span class="chip amber" style="height:18px;font-size:10px;padding:0 6px;margin-left:4px">Late</span>
                                @endif
                            </td>
                            <td>
                                <span class="mono" style="font-size:12.5px;color:{{ $att->check_out ? 'var(--text)' : 'var(--text-3)' }}">
                                    {{ $att->check_out ? substr($att->check_out, 0, 5) : '—' }}
                                </span>
                            </td>
                            <td class="r">
                                <span class="tnum" style="font-size:13px;font-weight:700;color:var(--gold)">
                                    {{ number_format($att->time_worked ?? 0, 1) }} hrs
                                </span>
                            </td>
                            <td class="r">
                                @if ($att->over_time > 0)
                                    <span class="chip violet" style="height:20px;font-size:10.5px">
                                        +{{ number_format($att->over_time, 1) }} hrs
                                    </span>
                                @else
                                    <span style="font-size:12px;color:var(--text-3)">—</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="chip {{ $isPresent ? 'green' : 'red' }}" wire:click="toggleStatus({{ $att->attendance_id }})" title="Click to toggle status" style="cursor:pointer;border:none">
                                    <span class="dot"></span>{{ ucfirst($att->status) }}
                                </button>
                            </td>
                            <td>
                                <span style="font-size:12px;color:var(--text-2)">{{ $att->description ?: 'Atelier bench work' }}</span>
                            </td>
                            <td class="r">
                                <div style="display:inline-flex;gap:6px">
                                    @if (!$att->check_out && $isPresent)
                                        <button type="button" class="btn btn-ghost btn-sm" wire:click="quickCheckOut({{ $user?->id }})" title="Clock Out Now" style="color:var(--amber)">
                                            <i class="bi bi-box-arrow-right"></i> Out
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="editAttendance({{ $att->attendance_id }})" title="Edit Entry">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-ghost btn-sm" wire:click="deleteAttendance({{ $att->attendance_id }})" wire:confirm="Delete this attendance record?" title="Delete" style="color:var(--rose)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center;padding:48px 20px;color:var(--text-3)">
                                <i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:8px;opacity:.5"></i>
                                No attendance records match your filter.
                                <div style="margin-top:12px">
                                    <button type="button" class="btn btn-sm btn-gold" wire:click="openManualAttendanceModal">
                                        <i class="bi bi-clock-history"></i> Log Attendance Entry
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
        {{ $attendances->links() }}
    </div>

    <!-- ================= ATTENDANCE MODAL ================= -->
    @if ($showAttendanceModal)
        <div class="dlg-backdrop" wire:click="closeModal"></div>
        <div class="dlg" role="dialog" aria-modal="true" style="max-width:560px" @keydown.escape.window="$wire.closeModal()">
            <header class="dlg-head">
                <div>
                    <span class="dlg-kicker">TIMEKEEPING ENTRY</span>
                    <h2 class="dlg-title">{{ $attendanceId ? 'Edit Attendance Record' : 'Log Artisan Attendance' }}</h2>
                </div>
                <button type="button" class="dlg-close" wire:click="closeModal" aria-label="Close dialog"><i class="bi bi-x-lg"></i></button>
            </header>

            <form wire:submit.prevent="saveAttendance">
                <div class="dlg-body" style="gap:14px">
                    <div class="field">
                        <label for="f-artisan">Select Artisan *</label>
                        <select id="f-artisan" class="select" wire:model="user_id" required>
                            <option value="">-- Choose Artisan --</option>
                            @foreach ($artisans as $a)
                                <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->email }})</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-adate">Date *</label>
                            <input id="f-adate" type="date" class="input" wire:model="date" required>
                            @error('date') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="f-astatus">Presence Status *</label>
                            <select id="f-astatus" class="select" wire:model="status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-cin">Check In Time</label>
                            <input id="f-cin" type="time" class="input" wire:model="check_in">
                        </div>
                        <div class="field">
                            <label for="f-cout">Check Out Time</label>
                            <input id="f-cout" type="time" class="input" wire:model="check_out">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="f-worked">Time Worked (Hours)</label>
                            <div class="input-wrap">
                                <input id="f-worked" type="number" step="0.25" min="0" class="input" wire:model="time_worked">
                                <span class="unit">hrs</span>
                            </div>
                        </div>
                        <div class="field">
                            <label for="f-aot">Overtime (Hours)</label>
                            <div class="input-wrap">
                                <input id="f-aot" type="number" step="0.25" min="0" class="input" wire:model="over_time">
                                <span class="unit">hrs</span>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="f-pstatus">Punctuality</label>
                        <select id="f-pstatus" class="select" wire:model="present_status">
                            <option value="ontime">On Time</option>
                            <option value="late">Late</option>
                            <option value="early">Early Departure</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="f-adesc">Workstation / Bench Notes</label>
                        <input id="f-adesc" class="input" wire:model="description" placeholder="e.g. Bench 04 · Setting sapphire halo stones">
                    </div>
                </div>

                <div class="dlg-foot" style="justify-content:flex-end;gap:8px">
                    <button type="button" class="btn btn-ghost" wire:click="closeModal">Cancel</button>
                    <button type="submit" class="btn btn-gold">
                        <i class="bi bi-check-lg"></i> Save Attendance
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
