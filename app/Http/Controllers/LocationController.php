<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $locations = Location::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('city','like',"%$q%")
                      ->orWhere('province','like',"%$q%")
                      ->orWhere('country','like',"%$q%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('locations.index', compact('locations','q'));
    }

    public function create(): View
    {
        $location = new Location();
        return view('locations.create', compact('location'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'city' => ['required','string','max:100'],
            'province' => ['nullable','string','max:100'],
            'country' => ['required','string','max:100'],
        ]);

        // unique combination (city, province, country)
        $exists = Location::where('city',$validated['city'])
            ->where('province',$validated['province'] ?? null)
            ->where('country',$validated['country'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['city' => 'Location already exists.']);
        }

        Location::create($validated);
        return redirect()->route('locations.index')->with('status','created');
    }

    public function edit(Location $location): View
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'city' => ['required','string','max:100'],
            'province' => ['nullable','string','max:100'],
            'country' => ['required','string','max:100'],
        ]);

        $exists = Location::where('city',$validated['city'])
            ->where('province',$validated['province'] ?? null)
            ->where('country',$validated['country'])
            ->where('id','<>',$location->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['city' => 'Location already exists.']);
        }

        $location->update($validated);
        return redirect()->route('locations.index')->with('status','updated');
    }

    public function destroy(Location $location): RedirectResponse
    {
        // prevent delete if referenced by rates or shipments
        if ($location->originRates()->exists() || $location->destinationRates()->exists()) {
            return back()->withErrors(['location' => 'Location is used by rates and cannot be deleted.']);
        }

        if ($location->hasMany(\App\Models\Shipment::class,'origin_id')->exists() ||
            $location->hasMany(\App\Models\Shipment::class,'destination_id')->exists()) {
            return back()->withErrors(['location' => 'Location is used by shipments and cannot be deleted.']);
        }

        $location->delete();
        return redirect()->route('locations.index')->with('status','deleted');
    }
}

