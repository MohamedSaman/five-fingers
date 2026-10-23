<?php

namespace App\Livewire\Production\Admin\HR;

use App\Models\Salary;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Attendance;
use App\Models\StageExpense;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class SalaryManagement extends Component
{
    use WithPagination;

    // Filters
    public $selectedMonth;
    public $selectedYear;
    public $statusFilter = '';
    public $search = '';

    // Modals
    public $showAdjustModal = false;
    public $showPayslipModal = false;

    // Adjust Salary Form
    public $salaryId = null;
    public $artisanName = '';
    public $basic_salary = 0;
    public $bonus = 0;
    public $allowance = 0;
    public $overtime = 0;
    public $deductions = 0;
    public $payment_status = 'pending';

    // Payslip viewer
    public $payslip = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->selectedMonth = (int) date('m');
        $this->selectedYear = (int) date('Y');
    }

    public function updatingSelectedMonth()
    {
        $this->resetPage();
    }

    public function updatingSelectedYear()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateMonthlyPayroll()
    {
        $targetDate = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->toDateString();
        $staffMembers = User::where('role', 'staff')->with('userDetail')->get();

        $generatedCount = 0;
        foreach ($staffMembers as $staff) {
            $basic = (float) ($staff->userDetail?->basic_salary ?? 80000);

            // Fetch stage labor incentives earned this month
            $stageLabor = (float) StageExpense::where('recorded_by', $staff->id)
                ->where('expense_type', 'labor')
                ->whereMonth('created_at', $this->selectedMonth)
                ->whereYear('created_at', $this->selectedYear)
                ->sum('amount');

            // Fetch attendance overtime hours
            $overtimeHours = (float) Attendance::where('user_id', $staff->id)
                ->whereMonth('date', $this->selectedMonth)
                ->whereYear('date', $this->selectedYear)
                ->sum('over_time');

            $hourlyRate = $basic / 200; // standard 200 monthly hours
            $otPay = round($overtimeHours * $hourlyRate * 1.5, 2);
            $allowance = 5000;
            $bonus = $stageLabor > 0 ? $stageLabor : 2500;
            $deductions = 2000; // statutory EPF/tax/advance

            $net = $basic + $bonus + $allowance + $otPay - $deductions;

            Salary::updateOrCreate([
                'user_id' => $staff->id,
                'salary_month' => $targetDate,
            ], [
                'salary_type' => 'monthly',
                'basic_salary' => $basic,
                'bonus' => $bonus,
                'allowance' => $allowance,
                'overtime' => $otPay,
                'deductions' => $deductions,
                'net_salary' => $net,
                'total_hours' => 176 + $overtimeHours,
                'overtime_hours' => $overtimeHours,
                'payment_status' => 'pending',
            ]);

            $generatedCount++;
        }

        $monthName = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->format('F Y');
        $this->dispatch('toast', type: 'success', message: "Generated payroll for {$generatedCount} artisans for {$monthName}!");
    }

    public function togglePaymentStatus($id)
    {
        $salary = Salary::findOrFail($id);
        $newStatus = $salary->payment_status === 'paid' ? 'pending' : 'paid';
        $salary->update(['payment_status' => $newStatus]);

        $this->dispatch('toast', type: $newStatus === 'paid' ? 'success' : 'info', message: "Payment status set to {$newStatus}.");
    }

    public function editSalary($id)
    {
        $salary = Salary::with('user')->findOrFail($id);
        $this->salaryId = $salary->salary_id;
        $this->artisanName = $salary->user->name ?? 'Artisan';
        $this->basic_salary = (float) $salary->basic_salary;
        $this->bonus = (float) $salary->bonus;
        $this->allowance = (float) $salary->allowance;
        $this->overtime = (float) $salary->overtime;
        $this->deductions = (float) $salary->deductions;
        $this->payment_status = $salary->payment_status;

        $this->showAdjustModal = true;
    }

    public function saveSalaryAdjustment()
    {
        $this->validate([
            'basic_salary' => 'required|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'overtime' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'payment_status' => 'required|string|in:paid,pending,failed',
        ]);

        $salary = Salary::findOrFail($this->salaryId);
        $net = (float) $this->basic_salary + (float) $this->bonus + (float) $this->allowance + (float) $this->overtime - (float) $this->deductions;

        $salary->update([
            'basic_salary' => $this->basic_salary,
            'bonus' => $this->bonus ?: 0,
            'allowance' => $this->allowance ?: 0,
            'overtime' => $this->overtime ?: 0,
            'deductions' => $this->deductions ?: 0,
            'net_salary' => max(0, $net),
            'payment_status' => $this->payment_status,
        ]);

        $this->showAdjustModal = false;
        $this->dispatch('toast', type: 'success', message: "Salary adjustments saved for {$this->artisanName}!");
    }

    public function viewPayslip($id)
    {
        $this->payslip = Salary::with(['user.userDetail'])->findOrFail($id);
        $this->showPayslipModal = true;
    }

    public function deleteSalary($id)
    {
        Salary::where('salary_id', $id)->delete();
        $this->dispatch('toast', type: 'success', message: "Salary record deleted.");
    }

    public function closeModal()
    {
        $this->showAdjustModal = false;
        $this->showPayslipModal = false;
    }

    public function render()
    {
        $query = Salary::with(['user.userDetail'])
            ->whereMonth('salary_month', $this->selectedMonth)
            ->whereYear('salary_month', $this->selectedYear);

        if (!empty($this->statusFilter)) {
            $query->where('payment_status', $this->statusFilter);
        }

        if (!empty($this->search)) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $salaries = $query->orderBy('salary_id', 'desc')->paginate(12);

        // Summaries for this month
        $baseQuery = Salary::whereMonth('salary_month', $this->selectedMonth)
            ->whereYear('salary_month', $this->selectedYear);

        $totalPayroll = (float) (clone $baseQuery)->sum('net_salary');
        $totalPaid = (float) (clone $baseQuery)->where('payment_status', 'paid')->sum('net_salary');
        $totalPending = (float) (clone $baseQuery)->where('payment_status', 'pending')->sum('net_salary');
        $totalOvertime = (float) (clone $baseQuery)->sum('overtime');
        $totalBonus = (float) (clone $baseQuery)->sum('bonus');

        return view('livewire.production.admin.hr.salary-management', [
            'salaries' => $salaries,
            'totalPayroll' => $totalPayroll,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
            'totalOvertime' => $totalOvertime,
            'totalBonus' => $totalBonus,
        ])->layout('components.layouts.production', [
            'title' => 'Salary & Payroll · HR Management'
        ]);
    }
}
