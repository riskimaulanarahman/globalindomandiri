<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;
use App\Http\Resources\RateResource;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index(Request $request)
    {
        $q = Rate::with(['origin','destination'])->where('is_active', true);
        if ($o = $request->get('origin_id')) $q->where('origin_id', $o);
        if ($d = $request->get('destination_id')) $q->where('destination_id', $d);
        if ($s = $request->get('service_type')) $q->where('service_type', $s);
        return RateResource::collection($q->paginate(20));
    }

    public function store(StoreRateRequest $request)
    {
        $rate = Rate::create($request->validated());
        return new RateResource($rate->load(['origin','destination']));
    }

    public function show(Rate $rate)
    {
        return new RateResource($rate->load(['origin','destination']));
    }

    public function update(UpdateRateRequest $request, Rate $rate)
    {
        $rate->update($request->validated());
        return new RateResource($rate->load(['origin','destination']));
    }

    public function destroy(Rate $rate)
    {
        $rate->delete();
        return response()->noContent();
    }
}

