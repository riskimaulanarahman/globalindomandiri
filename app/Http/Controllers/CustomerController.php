<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $customers = Customer::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('code', 'like', "%$q%")
                      ->orWhere('name', 'like', "%$q%" );
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'q'));
    }

    public function create(): View
    {
        $customer = new Customer();
        return view('customers.create', compact('customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable','string','max:50','unique:customers,code'],
            'name' => ['required','string','max:255'],
            'npwp' => ['nullable','string','max:100'],
            'payment_term_days' => ['nullable','integer','min:0','max:365'],
            'credit_limit' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string','max:1000'],
            'contacts' => ['array'],
            'contacts.*.name' => ['required_with:contacts','string','max:255'],
            'contacts.*.phone' => ['nullable','string','max:50'],
            'contacts.*.email' => ['nullable','email','max:255'],
            'contacts.*.address' => ['nullable','string','max:1000'],
            'contacts.*.is_default' => ['nullable','boolean'],
            'contacts.*.notes' => ['nullable','string','max:1000'],
        ]);

        if (empty($validated['code'])) {
            $branch = env('APP_CODENAME', 'RGM');
            $validated['code'] = app(\App\Services\DocumentNumberService::class)->nextCustomerCode($branch);
        }

        $contacts = $validated['contacts'] ?? [];
        unset($validated['contacts']);

        $customer = Customer::create($validated);
        $this->syncContacts($customer, $contacts);
        return redirect()->route('customers.index')->with('status', 'created');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required','string','max:50','unique:customers,code,'.$customer->id],
            'name' => ['required','string','max:255'],
            'npwp' => ['nullable','string','max:100'],
            'payment_term_days' => ['nullable','integer','min:0','max:365'],
            'credit_limit' => ['nullable','numeric','min:0'],
            'notes' => ['nullable','string','max:1000'],
            'contacts' => ['array'],
            'contacts.*.name' => ['required_with:contacts','string','max:255'],
            'contacts.*.phone' => ['nullable','string','max:50'],
            'contacts.*.email' => ['nullable','email','max:255'],
            'contacts.*.address' => ['nullable','string','max:1000'],
            'contacts.*.is_default' => ['nullable','boolean'],
            'contacts.*.notes' => ['nullable','string','max:1000'],
        ]);

        $contacts = $validated['contacts'] ?? [];
        unset($validated['contacts']);

        $customer->update($validated);
        $this->syncContacts($customer, $contacts);
        return redirect()->route('customers.index')->with('status', 'updated');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        // Prevent deleting if has invoices with outstanding > 0
        $hasOutstanding = Invoice::where('customer_id', $customer->id)
            ->get()
            ->contains(fn($inv) => $inv->outstanding > 0);

        if ($hasOutstanding) {
            return back()->withErrors(['customer' => 'Customer has outstanding invoices and cannot be deleted.']);
        }

        // Prevent deleting if has any shipments or invoices
        if ($customer->shipments()->exists() || $customer->invoices()->exists()) {
            return back()->withErrors(['customer' => 'Customer has related records and cannot be deleted.']);
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('status', 'deleted');
    }

    public function contacts(Customer $customer)
    {
        $items = $customer->contacts()->orderByDesc('is_default')->orderBy('name')->get(['id','name','phone','email','address','is_default']);
        return response()->json(['items' => $items]);
    }

    private function syncContacts(Customer $customer, array $contacts): void
    {
        // Replace-all strategy for simplicity
        $customer->contacts()->delete();
        $hasDefault = false;
        foreach ($contacts as $c) {
            if (!isset($c['name']) || trim((string)$c['name']) === '') continue;
            $isDefault = (bool)($c['is_default'] ?? false);
            if ($isDefault && !$hasDefault) {
                $hasDefault = true;
            } else {
                // ensure only one default
                $isDefault = false;
            }
            CustomerContact::create([
                'customer_id' => $customer->id,
                'name' => trim((string)$c['name']),
                'phone' => $c['phone'] ?? null,
                'email' => $c['email'] ?? null,
                'address' => $c['address'] ?? null,
                'is_default' => $isDefault,
                'notes' => $c['notes'] ?? null,
            ]);
        }
    }
}
