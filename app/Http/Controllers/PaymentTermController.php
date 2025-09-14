<?php

namespace App\Http\Controllers;

use App\Models\PaymentTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentTermController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $terms = PaymentTerm::query()
            ->when($q !== '', function($qr) use ($q) {
                $qr->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('code','like',"%$q%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        return view('payment_terms.index', compact('terms','q'));
    }

    public function create(): View
    {
        $term = new PaymentTerm(['is_active' => 1]);
        return view('payment_terms.create', compact('term'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'code' => ['required','string','max:20','unique:payment_terms,code'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        PaymentTerm::create($data);
        return redirect()->route('payment-terms.index')->with('status','created');
    }

    public function edit(PaymentTerm $payment_term): View
    {
        return view('payment_terms.edit', ['term' => $payment_term]);
    }

    public function update(Request $request, PaymentTerm $payment_term): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'code' => ['required','string','max:20','unique:payment_terms,code,'.$payment_term->id],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $payment_term->update($data);
        return redirect()->route('payment-terms.index')->with('status','updated');
    }

    public function destroy(PaymentTerm $payment_term): RedirectResponse
    {
        if (\App\Models\Quotation::where('payment_term_id', $payment_term->id)->exists()) {
            return back()->withErrors(['payment_term' => 'Payment term is used by quotations and cannot be deleted.']);
        }
        $payment_term->delete();
        return redirect()->route('payment-terms.index')->with('status','deleted');
    }
}

