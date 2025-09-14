<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentPricingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $q = Shipment::with(['customer','origin','destination','items','rate']);
        if ($request->filled('status')) $q->where('status', $request->get('status'));
        if ($request->filled('customer_id')) $q->where('customer_id', $request->get('customer_id'));
        if ($request->filled('origin_id')) $q->where('origin_id', $request->get('origin_id'));
        if ($request->filled('destination_id')) $q->where('destination_id', $request->get('destination_id'));
        if ($request->filled('service_type')) $q->where('service_type', $request->get('service_type'));
        if ($request->filled('from')) $q->whereDate('created_at', '>=', $request->get('from'));
        if ($request->filled('to')) $q->whereDate('created_at', '<=', $request->get('to'));
        return ShipmentResource::collection($q->paginate(20));
    }

    public function store(StoreShipmentRequest $request, ShipmentPricingService $pricing)
    {
        $shipment = Shipment::create($request->validated());
        if ($request->has('items')) {
            foreach ($request->input('items') as $i => $item) {
                $shipment->items()->create(array_merge($item, ['koli_no' => $item['koli_no'] ?? ($i+1)]));
            }
        }
        $pricing->recomputeShipmentWeights($shipment->fresh('items'));
        $pricing->applyRateAndCharges($shipment->fresh());
        return new ShipmentResource($shipment->fresh(['items','origin','destination','customer','rate']));
    }

    public function show(Shipment $shipment)
    {
        return new ShipmentResource($shipment->load(['items','origin','destination','customer','rate']));
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment, ShipmentPricingService $pricing)
    {
        $shipment->update($request->validated());
        if ($request->has('items')) {
            foreach ($request->input('items') as $item) {
                $shipment->items()->updateOrCreate(['koli_no' => $item['koli_no']], $item);
            }
        }
        $pricing->recomputeShipmentWeights($shipment->fresh('items'));
        $pricing->applyRateAndCharges($shipment->fresh());
        return new ShipmentResource($shipment->fresh(['items','origin','destination','customer','rate']));
    }

    public function transition(Request $request, Shipment $shipment)
    {
        $request->validate(['status' => ['required', Rule::in(['Draft','ReceivedAtOrigin','InTransit','ReceivedAtDestination','Delivered','Cancelled'])]]);
        $from = $shipment->status;
        $to = $request->get('status');
        // Simple guard for allowed transitions
        $allowed = [
            'Draft' => ['ReceivedAtOrigin','Cancelled'],
            'ReceivedAtOrigin' => ['InTransit','Cancelled'],
            'InTransit' => ['ReceivedAtDestination','Cancelled'],
            'ReceivedAtDestination' => ['Delivered','Cancelled'],
        ];
        if (isset($allowed[$from]) && in_array($to, $allowed[$from])) {
            $shipment->status = $to;
            $shipment->save();
        } else {
            abort(422, 'Invalid status transition');
        }
        return new ShipmentResource($shipment);
    }
}

