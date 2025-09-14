<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Service;
use App\Models\TermsAndCondition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TermsAndConditionController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $serviceId = $request->integer('service_id');
        $active = $request->get('active');

        $items = TermsAndCondition::with('services')
            ->when($q !== '', function($qr) use ($q){
                $qr->where(function($w) use ($q){
                    $w->where('title','like',"%$q%")
                      ->orWhere('version','like',"%$q%")
                      ->orWhere('body','like',"%$q%")
                      ;
                });
            })
            ->when($serviceId, fn($qr) => $qr->whereHas('services', fn($w) => $w->where('services.id', $serviceId)))
            ->when($active !== null && $active !== '', fn($qr) => $qr->where('is_active', (bool)$active))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $services = Service::orderBy('name')->get();
        return view('terms_conditions.index', compact('items','services','q','serviceId','active'));
    }

    public function create(): View
    {
        $tnc = new TermsAndCondition(['is_active' => 1]);
        $services = Service::orderBy('name')->get();
        return view('terms_conditions.create', compact('tnc','services'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $services = $data['services'] ?? [];
        unset($data['services']);
        // Ensure service_id is nullable by design now
        $data['service_id'] = null;
        $tnc = TermsAndCondition::create($data);
        if (!empty($services)) {
            $tnc->services()->sync($services);
        }
        return redirect()->route('terms-conditions.index')->with('status','created');
    }

    public function edit(TermsAndCondition $terms_condition): View
    {
        $terms_condition->load('services');
        $services = Service::orderBy('name')->get();
        return view('terms_conditions.edit', ['tnc' => $terms_condition, 'services' => $services]);
    }

    public function update(Request $request, TermsAndCondition $terms_condition): RedirectResponse
    {
        $data = $this->validateData($request);
        $services = $data['services'] ?? [];
        unset($data['services']);
        $data['service_id'] = null;
        $terms_condition->update($data);
        if (!empty($services)) {
            $terms_condition->services()->sync($services);
        } else {
            $terms_condition->services()->sync([]);
        }
        return redirect()->route('terms-conditions.index')->with('status','updated');
    }

    public function destroy(TermsAndCondition $terms_condition): RedirectResponse
    {
        if (Quotation::where('terms_and_conditions_id', $terms_condition->id)->exists()) {
            return back()->withErrors(['terms' => 'Cannot delete: this Terms & Conditions is used by some quotations.']);
        }
        $terms_condition->delete();
        return redirect()->route('terms-conditions.index')->with('status','deleted');
    }

    public function options(Request $request): JsonResponse
    {
        $serviceId = $request->integer('service_id');
        $items = TermsAndCondition::select('id','title','version','is_active','body')
            ->when($serviceId, fn($qr) => $qr->whereHas('services', fn($w) => $w->where('services.id', $serviceId)))
            ->where('is_active', 1)
            ->orderBy('title')
            ->get();
        return response()->json(['items' => $items]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required','string','max:150'],
            'body' => ['required','string'],
            'version' => ['nullable','string','max:50'],
            'effective_from' => ['nullable','date'],
            'effective_to' => ['nullable','date','after_or_equal:effective_from'],
            'is_active' => ['nullable','boolean'],
            'services' => ['required','array','min:1'],
            'services.*' => ['integer','exists:services,id'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        return $data;
    }
}

