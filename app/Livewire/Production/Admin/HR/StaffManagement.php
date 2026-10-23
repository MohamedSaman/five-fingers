<?php

namespace App\Livewire\Production\Admin\HR;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\Attendance;
use App\Models\ProductionStage;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class StaffManagement extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $departmentFilter = '';
    public $statusFilter = '';

    // Modal state
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showViewModal = false;

    // Form inputs
    public $staffId = null;
    public $name = '';
    public $email = '';
    public $password = '';
    public $department = 'Bench & Setting';
    public $work_role = 'Bench Goldsmith';
    public $basic_salary = 85000;
    public $phone = '';
    public $nic_num = '';
    public $address = '';
    public $gender = 'male';
    public $join_date = '';
    public $status = 'active';
    public $description = '';

    // Selected staff for viewing dossier
    public $selectedStaff = null;
    public $selectedStaffStages = [];
    public $selectedStaffAttendances = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->join_date = now()->toDateString();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetErrorBag();
        $this->staffId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->department = 'Bench & Setting';
        $this->work_role = 'Bench Goldsmith';
        $this->basic_salary = 85000;
        $this->phone = '';
        $this->nic_num = '';
        $this->address = 'Colombo Gold Souk Atelier';
        $this->gender = 'male';
        $this->join_date = now()->toDateString();
        $this->status = 'active';
        $this->description = '';

        $this->showCreateModal = true;
    }

    public function saveStaff()
    {
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:6',
                'department' => 'required|string',
                'work_role' => 'required|string',
                'basic_salary' => 'required|numeric|min:0',
                'nic_num' => 'nullable|string|max:50',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'gender' => 'required|string|in:male,female,other',
                'join_date' => 'nullable|date',
                'status' => 'required|string|in:active,inactive',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', type: 'error', message: 'Please check the form for errors.');
            throw $e;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'contact' => $this->phone ?: '+94 77 123 4567',
            'role' => 'staff',
            'module' => 'production',
        ]);

        UserDetail::create([
            'user_id' => $user->id,
            'department' => $this->department,
            'work_role' => $this->work_role,
            'basic_salary' => $this->basic_salary,
            'nic_num' => $this->nic_num,
            'address' => $this->address,
            'gender' => $this->gender,
            'join_date' => $this->join_date ?: now()->toDateString(),
            'status' => $this->status,
            'description' => $this->description,
        ]);

        $this->showCreateModal = false;
        $this->dispatch('toast', type: 'success', message: "Artisan {$this->name} created successfully!");
    }

    public function editStaff($id)
    {
        $this->resetErrorBag();
        $user = User::with('userDetail')->findOrFail($id);
        $detail = $user->userDetail;

        $this->staffId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->phone = $user->contact ?? '';
        $this->department = $detail?->department ?? 'Bench & Setting';
        $this->work_role = $detail?->work_role ?? 'Artisan';
        $this->basic_salary = (float) ($detail?->basic_salary ?? 80000);
        $this->nic_num = $detail?->nic_num ?? '';
        $this->address = $detail?->address ?? '';
        $this->gender = $detail?->gender ?? 'male';
        $this->join_date = $detail?->join_date ? $detail->join_date->toDateString() : now()->toDateString();
        $this->status = $detail?->status ?? 'active';
        $this->description = $detail?->description ?? '';

        $this->showEditModal = true;
    }

    public function updateStaff()
    {
        try {
            $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $this->staffId,
                'password' => 'nullable|min:6',
                'phone' => 'nullable|string|max:50',
                'department' => 'required|string',
                'work_role' => 'required|string',
                'basic_salary' => 'required|numeric|min:0',
                'gender' => 'required|string|in:male,female,other',
                'status' => 'required|string|in:active,inactive',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('toast', type: 'error', message: 'Please check the form for errors.');
            throw $e;
        }

        $user = User::findOrFail($this->staffId);
        $user->name = $this->name;
        $user->email = $this->email;
        if (!empty($this->phone)) {
            $user->contact = $this->phone;
        }
        if (!empty($this->password)) {
            $user->password = Hash::make($this->password);
        }
        $user->save();

        UserDetail::updateOrCreate(['user_id' => $user->id], [
            'department' => $this->department,
            'work_role' => $this->work_role,
            'basic_salary' => $this->basic_salary,
            'nic_num' => $this->nic_num,
            'address' => $this->address,
            'gender' => $this->gender,
            'join_date' => $this->join_date ?: now()->toDateString(),
            'status' => $this->status,
            'description' => $this->description,
        ]);

        $this->showEditModal = false;
        $this->dispatch('toast', type: 'success', message: "Artisan {$this->name} updated successfully!");
    }

    public function viewStaff($id)
    {
        $this->selectedStaff = User::with(['userDetail', 'attendances'])->findOrFail($id);
        $this->selectedStaffStages = ProductionStage::with('product')
            ->where('assigned_user_id', $id)
            ->latest()
            ->limit(8)
            ->get();
        $this->selectedStaffAttendances = Attendance::where('user_id', $id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        $this->showViewModal = true;
    }

    public function toggleStatus($id)
    {
        $detail = UserDetail::where('user_id', $id)->first();
        if ($detail) {
            $newStatus = $detail->status === 'active' ? 'inactive' : 'active';
            $detail->update(['status' => $newStatus]);
            $this->dispatch('toast', type: 'info', message: "Status changed to {$newStatus}.");
        }
    }

    public function deleteStaff($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            $this->dispatch('toast', type: 'error', message: "Cannot delete an administrator account.");
            return;
        }

        $user->delete();
        $this->dispatch('toast', type: 'success', message: "Staff record removed.");
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
    }

    public function render()
    {
        $query = User::with(['userDetail', 'attendances'])
            ->where('role', '!=', 'admin');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('userDetail', function ($dq) {
                      $dq->where('work_role', 'like', '%' . $this->search . '%')
                         ->orWhere('department', 'like', '%' . $this->search . '%')
                         ->orWhere('nic_num', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if (!empty($this->departmentFilter)) {
            $query->whereHas('userDetail', function ($q) {
                $q->where('department', $this->departmentFilter);
            });
        }

        if (!empty($this->statusFilter)) {
            $query->whereHas('userDetail', function ($q) {
                $q->where('status', $this->statusFilter);
            });
        }

        $staffMembers = $query->orderBy('name')->paginate(12);

        // Department list for filters
        $departments = [
            'Design & 3D',
            'Casting & Foundry',
            'Bench & Setting',
            'Finishing & Luster',
            'Quality Control',
            'Assembly',
        ];

        // Overall stats
        $allStaff = User::where('role', '!=', 'admin')->with('userDetail')->get();
        $totalCount = $allStaff->count();
        $activeCount = $allStaff->filter(fn ($u) => ($u->userDetail?->status ?? 'active') === 'active')->count();
        $totalPayroll = $allStaff->sum(fn ($u) => (float) ($u->userDetail?->basic_salary ?? 0));
        $departmentsCount = $allStaff->pluck('userDetail.department')->filter()->unique()->count();

        return view('livewire.production.admin.hr.staff-management', [
            'staffMembers' => $staffMembers,
            'departments' => $departments,
            'totalCount' => $totalCount,
            'activeCount' => $activeCount,
            'totalPayroll' => $totalPayroll,
            'departmentsCount' => $departmentsCount,
        ])->layout('components.layouts.production', [
            'title' => 'Staff & Artisans · HR Management'
        ]);
    }
}
