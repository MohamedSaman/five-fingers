<?php

namespace App\Livewire\Production\Admin;

use App\Models\StageExpense;
use App\Models\StageMetalLog;
use Livewire\Component;
use Livewire\WithPagination;

class ExpensesList extends Component
{
    use WithPagination;

    public $expenseType = '';
    public $search = '';

    public function mount()
    {
        $this->expenseType = request()->get('type', '');
    }

    public function render()
    {
        $query = StageExpense::with(['product', 'stage', 'recorder']);

        if (!empty($this->expenseType)) {
            $query->where('expense_type', $this->expenseType);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('product', function ($pq) {
                      $pq->where('sku', 'like', '%' . $this->search . '%')
                         ->orWhere('title', 'like', '%' . $this->search . '%');
                  });
            });
        }

        $expenses = $query->orderBy('id', 'desc')->paginate(15);

        // Summaries
        $totalLabor = StageExpense::where('expense_type', 'labor')->sum('amount');
        $totalConsumables = StageExpense::where('expense_type', 'consumable')->sum('amount');
        $totalMetalLoss = StageMetalLog::sum('loss_monetary_value');
        $grandTotal = $totalLabor + $totalConsumables + $totalMetalLoss;

        return view('livewire.production.admin.expenses-list', [
            'expenses' => $expenses,
            'totalLabor' => $totalLabor,
            'totalConsumables' => $totalConsumables,
            'totalMetalLoss' => $totalMetalLoss,
            'grandTotal' => $grandTotal,
        ])->layout('components.layouts.production', [
            'title' => 'Manufacturing Expenses & Costing'
        ]);
    }
}
