<?php

namespace App\Livewire\Production\Admin\HR;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceManagement extends Component
{
    use WithPagination;

    // Filters
    public $dateFilter = 'today';
    public $customDate = '';
    public $search = '';
    public $statusFilter = '';

    // Modal state
    public $showAttendanceModal = false;

    // Form inputs
    public $attendanceId = null;
    public $user_id = '';
    public $date = '';
    public $check_in = '08:30';
    public $check_out = '17:30';
    public $time_worked = 8.5;
    public $over_time = 0.5;
    public $status = 'present';
    public $present_status = 'ontime';
    public $description = '';

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->customDate = now()->toDateString();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openManualAttendanceModal($userId = null)
    {
        $this->resetErrorBag();
        $this->attendanceId = null;
        $this->user_id = $userId ?: (User::where('role', 'staff')->first()?->id ?? '');
        $this->date = now()->toDateString();
        $this->check_in = '08:30';
        $this->check_out = '17:30';
        $this->time_worked = 8.5;
        $this->over_time = 0.5;
        $this->status = 'present';
        $this->present_status = 'ontime';
        $this->description = 'Bench session at workshop.';

        $this->showAttendanceModal = true;
    }

    public function editAttendance($id)
    {
        $this->resetErrorBag();
        $att = Attendance::findOrFail($id);

        $this->attendanceId = $att->attendance_id;
        $this->user_id = $att->user_id;
        $this->date = $att->date;
        $this->check_in = $att->check_in ? substr($att->check_in, 0, 5) : '08:30';
        $this->check_out = $att->check_out ? substr($att->check_out, 0, 5) : '17:30';
        $this->time_worked = (float) $att->time_worked;
        $this->over_time = (float) $att->over_time;
        $this->status = $att->status;
        $this->present_status = $att->present_status ?? 'ontime';
        $this->description = $att->description ?? '';

        $this->showAttendanceModal = true;
    }

    public function saveAttendance()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'time_worked' => 'nullable|numeric|min:0',
            'over_time' => 'nullable|numeric|min:0',
            'present_status' => 'nullable|in:ontime,late,early',
        ]);

        if ($this->attendanceId) {
            $att = Attendance::findOrFail($this->attendanceId);
            $att->update([
                'user_id' => $this->user_id,
                'date' => $this->date,
                'check_in' => $this->check_in ? $this->check_in . ':00' : null,
                'check_out' => $this->check_out ? $this->check_out . ':00' : null,
                'time_worked' => $this->time_worked ?: 0,
                'over_time' => $this->over_time ?: 0,
                'status' => $this->status,
                'present_status' => $this->present_status,
                'description' => $this->description,
            ]);
            $this->dispatch('toast', type: 'success', message: 'Attendance record updated.');
        } else {
            Attendance::updateOrCreate([
                'user_id' => $this->user_id,
                'date' => $this->date,
            ], [
                'check_in' => $this->check_in ? $this->check_in . ':00' : null,
                'check_out' => $this->check_out ? $this->check_out . ':00' : null,
                'time_worked' => $this->time_worked ?: 0,
                'over_time' => $this->over_time ?: 0,
                'status' => $this->status,
                'present_status' => $this->present_status,
                'description' => $this->description,
            ]);
            $this->dispatch('toast', type: 'success', message: 'Attendance logged successfully.');
        }

        $this->showAttendanceModal = false;
    }

    public function quickCheckIn($userId)
    {
        $today = now()->toDateString();
        $timeNow = now()->format('H:i:s');

        Attendance::updateOrCreate([
            'user_id' => $userId,
            'date' => $today,
        ], [
            'check_in' => $timeNow,
            'status' => 'present',
            'present_status' => now()->format('H:i') > '09:00' ? 'late' : 'ontime',
            'time_worked' => 8.0,
            'description' => 'Quick clock-in via Atelier console',
        ]);

        $user = User::find($userId);
        $this->dispatch('toast', type: 'success', message: "Clock-in recorded for {$user?->name} at {$timeNow}.");
    }

    public function quickCheckOut($userId)
    {
        $today = now()->toDateString();
        $timeNow = now()->format('H:i:s');

        $att = Attendance::where('user_id', $userId)->whereDate('date', $today)->first();
        if ($att) {
            $in = $att->check_in ? Carbon::parse($att->date . ' ' . $att->check_in) : now()->subHours(8);
            $hours = max(0, round($in->diffInMinutes(now()) / 60, 2));
            $ot = max(0, round($hours - 8.0, 2));

            $att->update([
                'check_out' => $timeNow,
                'time_worked' => $hours,
                'over_time' => $ot,
            ]);
            $this->dispatch('toast', type: 'success', message: "Clock-out recorded at {$timeNow} ({$hours} hrs).");
        }
    }

    public function toggleStatus($id)
    {
        $att = Attendance::findOrFail($id);
        $new = $att->status === 'present' ? 'absent' : 'present';
        $att->update(['status' => $new]);
        $this->dispatch('toast', type: 'info', message: "Status set to {$new}.");
    }

    public function deleteAttendance($id)
    {
        Attendance::where('attendance_id', $id)->delete();
        $this->dispatch('toast', type: 'success', message: 'Attendance entry deleted.');
    }

    public function closeModal()
    {
        $this->showAttendanceModal = false;
    }

    public function render()
    {
        $query = Attendance::with(['user.userDetail']);

        // Date filter
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        switch ($this->dateFilter) {
            case 'today':
                $query->whereDate('date', $today);
                break;
            case 'yesterday':
                $query->whereDate('date', $yesterday);
                break;
            case 'this_week':
                $query->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                break;
            case 'this_month':
                $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
                break;
            case 'custom':
                if (!empty($this->customDate)) {
                    $query->whereDate('date', $this->customDate);
                }
                break;
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('check_in', 'asc')->paginate(15);

        // Daily statistics
        $allStaffCount = User::where('role', 'staff')->count();
        $todayLogs = Attendance::whereDate('date', $today)->get();
        $presentTodayCount = $todayLogs->where('status', 'present')->count();
        $onTimeCount = $todayLogs->where('present_status', 'ontime')->count();
        $totalHoursToday = $todayLogs->where('status', 'present')->sum('time_worked');
        $overtimeHoursToday = $todayLogs->sum('over_time');

        return view('livewire.production.admin.hr.attendance-management', [
            'attendances' => $attendances,
            'artisans' => User::where('role', 'staff')->orderBy('name')->get(),
            'allStaffCount' => $allStaffCount,
            'presentTodayCount' => $presentTodayCount,
            'onTimeCount' => $onTimeCount,
            'totalHoursToday' => $totalHoursToday,
            'overtimeHoursToday' => $overtimeHoursToday,
        ])->layout('components.layouts.production', [
            'title' => 'Artisan Attendance · HR Management'
        ]);
    }
}
