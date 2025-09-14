<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string)$request->get('q',''));
        $services = Service::query()
            ->when($q !== '', function($qr) use ($q) {
                $qr->where(function($w) use ($q){
                    $w->where('name','like',"%$q%")
                      ->orWhere('code','like',"%$q%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();
        return view('services.index', compact('services','q'));
    }

    public function create(): View
    {
        $service = new Service(['is_active' => 1]);
        return view('services.create', compact('service'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'code' => ['required','string','max:50','unique:services,code'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        Service::create($data);
        return redirect()->route('services.index')->with('status','created');
    }

    public function edit(Service $service): View
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:120'],
            'code' => ['required','string','max:50','unique:services,code,'.$service->id],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $service->update($data);
        return redirect()->route('services.index')->with('status','updated');
    }

    public function destroy(Service $service): RedirectResponse
    {
        // Basic guard: if referenced by quotations, safer to prevent delete
        if (\App\Models\Quotation::where('service_id', $service->id)->exists()) {
            return back()->withErrors(['service' => 'Service is used by quotations and cannot be deleted.']);
        }
        $service->delete();
        return redirect()->route('services.index')->with('status','deleted');
    }
}

