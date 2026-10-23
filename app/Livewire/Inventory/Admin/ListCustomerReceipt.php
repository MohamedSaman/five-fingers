<?php

namespace App\Livewire\Inventory\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Sale;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\WithDynamicLayout;
use Barryvdh\DomPDF\Facade\Pdf;

#[Title('List Customer Receipt')]
class ListCustomerReceipt extends Component
{
    use WithDynamicLayout;

    use WithPagination;

    public $showPaymentModal = false;
    public $selectedCustomer = null;
    public $payments = [];

    public function getCustomersProperty()
    {
        // Get customers with total paid and receipt count (sum from payments table)
        return Customer::select(
            'customers.id',
            'customers.name',
            'customers.email',
            'customers.address',
            'customers.created_at',
            'customers.updated_at'
        )
            ->selectRaw('COALESCE(SUM(payments.amount),0) as total_paid')
            ->selectRaw('COUNT(payments.id) as receipts_count')
            ->leftJoin('payments', 'payments.customer_id', '=', 'customers.id')
            ->groupBy(
                'customers.id',
                'customers.name',
                'customers.email',
                'customers.address',
                'customers.created_at',
                'customers.updated_at'
            )
            ->having('total_paid', '>', 0)
            ->orderByDesc('total_paid')
            ->paginate(20);
    }

    public function showCustomerPayments($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->payments = Payment::with(['allocations', 'allocations.sale', 'cheques'])
            ->where('customer_id', $customerId)
            ->orderByDesc('payment_date')
            ->get();
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->selectedCustomer = null;
        $this->payments = [];
    }

    public function downloadReceipt($paymentId)
    {
        $payment = Payment::with(['customer', 'allocations.sale', 'cheques'])->find($paymentId);
        
        if (!$payment) return;

        $pdf = Pdf::loadView('admin.receipts.payment-receipt', [
            'payment' => $payment,
            'customer' => $payment->customer,
            'allocations' => $payment->allocations->map(function($allocation) {
                return (object)[
                    'invoice_number' => $allocation->sale->invoice_number,
                    'total_amount' => $allocation->sale->total_amount,
                    'allocated_amount' => $allocation->allocated_amount,
                ];
            }),
            'received_by' => 'Staff', 
        ])->setPaper('a4');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'receipt-' . $payment->id . '.pdf');
    }

    public function render()
    {
        return view('livewire.inventory.admin.list-customer-receipt', [
            'customers' => $this->customers,
            'showPaymentModal' => $this->showPaymentModal,
            'selectedCustomer' => $this->selectedCustomer,
            'payments' => $this->payments,
        ])->layout($this->layout);
    }
}
