<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $q = Location::query();
        if ($s = $request->get('search')) {
            $q->where('city','like',"%$s%")->orWhere('province','like',"%$s%");
        }
        return LocationResource::collection($q->paginate(20));
    }

    public function store(StoreLocationRequest $request)
    {
        $loc = Location::create($request->validated());
        return new LocationResource($loc);
    }

    public function show(Location $location)
    {
        return new LocationResource($location);
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->validated());
        return new LocationResource($location);
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return response()->noContent();
    }
}

