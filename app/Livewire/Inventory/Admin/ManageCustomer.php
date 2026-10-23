<?php

namespace App\Livewire\Inventory\Admin;

use App\Models\Customer;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\ReturnsProduct;
use Livewire\Component;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Livewire\Concerns\WithDynamicLayout;
use Illuminate\Support\Facades\DB;

#[Title('Manage Customer')]
class ManageCustomer extends Component
{
    use WithDynamicLayout;

    public $name;
    public $contactNumber;
    public $address;
    public $email;
    public $customerType;
    public $businessName;

    public $editCustomerId;
    public $editName;
    public $editContactNumber;
    public $editAddress;
    public $editEmail;
    public $editCustomerType;
    public $editBusinessName;

    public $deleteId;
    public $showEditModal   = false;
    public $showCreateModal = false;
    public $showDeleteModal = false;
    public $showViewModal   = false;
    public $viewCustomerDetail = [];
    public $perPage = 10;

    // View modal tab state
    public $viewTab = 'overview'; // overview | sales | payments | dues | ledger

    // Data bags for view modal tabs
    public $viewSales       = [];
    public $viewPayments    = [];
    public $viewDues        = [];
    public $viewLedger      = [];
    public $viewSummary     = [];

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::latest()->paginate($this->perPage);
        return view('livewire.inventory.admin.manage-customer', [
            'customers' => $customers,
        ])->layout($this->layout);
    }

    /** ----------------------------
     * Create Customer
     * ---------------------------- */
    public function createCustomer()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'contactNumber', 'address', 'email', 'customerType', 'businessName',
            'editCustomerId', 'editName', 'editContactNumber', 'editAddress',
            'editEmail', 'editCustomerType', 'editBusinessName',
        ]);
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal   = false;
        $this->showDeleteModal = false;
        $this->showViewModal   = false;
        $this->viewTab         = 'overview';
        $this->resetForm();
    }

    /** ----------------------------
     * View Customer Details (with tabs)
     * ---------------------------- */
    public function viewDetails($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            $this->js("Swal.fire('Error!', 'Customer Not Found', 'error')");
            return;
        }

        $this->viewCustomerDetail = [
            'id'            => $customer->id,
            'name'          => $customer->name,
            'business_name' => $customer->business_name,
            'phone'         => $customer->phone,
            'email'         => $customer->email,
            'type'          => $customer->type,
            'address'       => $customer->address,
            'created_at'    => $customer->created_at,
            'updated_at'    => $customer->updated_at,
        ];

        $this->loadCustomerData($customer->id);

        $this->viewTab        = 'overview';
        $this->showViewModal  = true;
    }

    /** Switch tabs inside the view modal */
    public function switchTab($tab)
    {
        $this->viewTab = $tab;
    }

    /** Load all tab data for a customer */
    private function loadCustomerData($customerId)
    {
        // --- Sales ---
        $sales = Sale::where('customer_id', $customerId)
            ->orderByDesc('created_at')
            ->get();

        $this->viewSales = $sales->map(fn($s) => [
            'id'             => $s->id,
            'invoice_number' => $s->invoice_number,
            'date'           => $s->created_at->format('M d, Y'),
            'total_amount'   => $s->total_amount,
            'payment_status' => $s->payment_status,
            'status'         => $s->status,
            'due_amount'     => $s->due_amount ?? 0,
        ])->toArray();

        // --- Payments ---
        // Fetch by customer_id OR by any of the customer's sale_ids.
        // This catches standalone/partial payments where sale_id may be NULL
        // but the payment is still tagged to the customer.
        $saleIds = $sales->pluck('id');
        $payments = Payment::where(function ($q) use ($customerId, $saleIds) {
                $q->where('customer_id', $customerId);
                if ($saleIds->isNotEmpty()) {
                    $q->orWhereIn('sale_id', $saleIds);
                }
            })
            ->orderByDesc('created_at')
            ->get()
            ->unique('id'); // prevent duplicates if a payment matches both conditions

        $this->viewPayments = $payments->map(fn($p) => [
            'id'             => $p->id,
            'invoice_number' => optional($p->sale)->invoice_number ?? 'Direct Payment',
            'date'           => $p->created_at->format('M d, Y'),
            'amount'         => $p->amount,
            'method'         => $p->payment_method,
            'status'         => $p->status,
        ])->values()->toArray();

        // --- Dues ---
        $this->viewDues = $sales->filter(fn($s) => ($s->due_amount ?? 0) > 0)
            ->map(fn($s) => [
                'invoice_number' => $s->invoice_number,
                'date'           => $s->created_at->format('M d, Y'),
                'total_amount'   => $s->total_amount,
                'due_amount'     => $s->due_amount,
                'payment_status' => $s->payment_status,
            ])->values()->toArray();

        // --- Summary ---
        $totalSalesAmount  = $sales->sum('total_amount');
        $totalPaid         = $payments->sum('amount');
        $totalDue          = $sales->sum('due_amount');
        $totalReturns      = ReturnsProduct::whereIn('sale_id', $saleIds)->sum('total_amount');

        $this->viewSummary = [
            'total_sales'        => $sales->count(),
            'total_payments'     => $payments->count(),
            'pending_dues'       => $this->viewDues ? count($this->viewDues) : 0,
            'total_sales_amount' => $totalSalesAmount,
            'total_paid'         => $totalPaid,
            'total_due'          => $totalDue,
            'total_returns'      => $totalReturns,
        ];

        // --- Ledger (chronological debit/credit log) ---
        $ledger = [];

        // Add each sale as a debit entry
        foreach ($sales as $s) {
            $ledger[] = [
                'date'        => $s->created_at,
                'type'        => 'sale',
                'description' => 'Invoice #' . $s->invoice_number,
                'debit'       => $s->total_amount,
                'credit'      => 0,
            ];
        }

        // Add each payment as a credit entry
        foreach ($payments as $p) {
            $ledger[] = [
                'date'        => $p->created_at,
                'type'        => 'payment',
                'description' => 'Payment (' . ucfirst($p->payment_method) . ') — ' . (optional($p->sale)->invoice_number ? 'Inv#' . $p->sale->invoice_number : 'Direct Payment'),
                'debit'       => 0,
                'credit'      => $p->amount,
            ];
        }

        // Add returns as credit entries
        $returns = ReturnsProduct::whereIn('sale_id', $saleIds)
            ->with('sale')
            ->orderByDesc('created_at')
            ->get();

        foreach ($returns as $r) {
            $ledger[] = [
                'date'        => $r->created_at,
                'type'        => 'return',
                'description' => 'Return — Inv#' . optional($r->sale)->invoice_number,
                'debit'       => 0,
                'credit'      => $r->total_amount,
            ];
        }

        // Sort by date ascending and compute running balance
        usort($ledger, fn($a, $b) => $a['date'] <=> $b['date']);

        $balance = 0;
        foreach ($ledger as &$entry) {
            $balance += $entry['debit'] - $entry['credit'];
            $entry['balance'] = $balance;
            $entry['date']    = \Carbon\Carbon::parse($entry['date'])->format('M d, Y');
        }
        unset($entry);

        $this->viewLedger = $ledger;
    }

    public function saveCustomer()
    {
        $this->validate([
            'name'         => 'required',
            'customerType' => 'required',
            'contactNumber'=> 'nullable|max:10',
            'address'      => 'nullable',
            'email'        => 'nullable|email|unique:customers,email',
            'businessName' => 'nullable',
        ]);

        try {
            Customer::create([
                'name'          => $this->name,
                'phone'         => $this->contactNumber,
                'address'       => $this->address,
                'email'         => $this->email,
                'type'          => $this->customerType,
                'business_name' => $this->businessName,
            ]);

            $this->js("Swal.fire('Success!', 'Customer Created Successfully', 'success')");
            $this->closeModal();
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . $e->getMessage() . "', 'error')");
        }
    }

    /** ----------------------------
     * Edit Customer
     * ---------------------------- */
    public function editCustomer($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            $this->js("Swal.fire('Error!', 'Customer not found.', 'error')");
            return;
        }

        $this->editCustomerId    = $customer->id;
        $this->editName          = $customer->name;
        $this->editContactNumber = $customer->phone;
        $this->editBusinessName  = $customer->business_name;
        $this->editCustomerType  = $customer->type;
        $this->editAddress       = $customer->address;
        $this->editEmail         = $customer->email;

        $this->showEditModal = true;
    }

    public function updateCustomer()
    {
        $this->validate([
            'editName'          => 'required',
            'editCustomerType'  => 'required',
            'editBusinessName'  => 'nullable',
            'editContactNumber' => 'nullable|max:10',
            'editAddress'       => 'nullable',
            'editEmail'         => 'nullable|email|unique:customers,email,' . $this->editCustomerId,
        ]);

        try {
            $customer = Customer::find($this->editCustomerId);
            if (!$customer) {
                $this->js("Swal.fire('Error!', 'Customer not found.', 'error')");
                return;
            }

            $customer->update([
                'name'          => $this->editName,
                'phone'         => $this->editContactNumber,
                'business_name' => $this->editBusinessName,
                'type'          => $this->editCustomerType,
                'address'       => $this->editAddress,
                'email'         => $this->editEmail,
            ]);

            $this->js("Swal.fire('Success!', 'Customer Updated Successfully', 'success')");
            $this->closeModal();
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . $e->getMessage() . "', 'error')");
        }
    }

    /** ----------------------------
     * Delete Customer
     * ---------------------------- */
    public function confirmDelete($id)
    {
        $this->deleteId        = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deleteId        = null;
    }

    public function deleteCustomer()
    {
        try {
            Customer::where('id', $this->deleteId)->delete();
            $this->js("Swal.fire('Success!', 'Customer deleted successfully.', 'success')");
            $this->dispatch('refreshPage');
            $this->cancelDelete();
        } catch (Exception $e) {
            $this->js("Swal.fire('Error!', '" . $e->getMessage() . "', 'error')");
        }
    }
}