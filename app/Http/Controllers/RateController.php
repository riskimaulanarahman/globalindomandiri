<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Rate;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RateController extends Controller
{
    public function index(Request $request): View
    {
        $originId = $request->integer('origin_id');
        $destinationId = $request->integer('destination_id');
        $service = trim((string) $request->get('service_type', ''));
        $active = $request->get('active'); // '1' or '0' or null

        $rates = Rate::with(['origin','destination'])
            ->when($originId, fn($q) => $q->where('origin_id', $originId))
            ->when($destinationId, fn($q) => $q->where('destination_id', $destinationId))
            ->when($service !== '', fn($q) => $q->where('service_type', 'like', "%$service%"))
            ->when($active !== null && $active !== '', fn($q) => $q->where('is_active', (bool)$active))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $locations = Location::orderBy('city')->get();

        return view('rates.index', compact('rates','locations','originId','destinationId','service','active'));
    }

    public function create(): View
    {
        $rate = new Rate(['is_active' => true]);
        $locations = Location::orderBy('city')->get();
        $services = Service::where('is_active', 1)->orderBy('name')->get();
        $serviceOptions = $this->serviceOptionsFrom($services);
        return view('rates.create', compact('rate','locations','services','serviceOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin_id' => ['required','integer','exists:locations,id'],
            'destination_id' => ['required','integer','exists:locations,id','different:origin_id'],
            'service_type' => ['required','string','max:100'],
            'price' => ['required','numeric','min:0'],
            'lead_time' => ['nullable','string','max:50'],
            'min_weight' => ['nullable','integer','min:0'],
            'max_weight' => ['nullable','integer','min:0'],
            'is_active' => ['nullable','boolean'],
        ]);

        // prevent duplicate (origin, destination, service_type)
        $exists = Rate::where('origin_id',$validated['origin_id'])
            ->where('destination_id',$validated['destination_id'])
            ->where('service_type',$validated['service_type'])
            ->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['service_type' => 'Rate with same origin, destination and service already exists.']);
        }

        $validated['is_active'] = (bool)($validated['is_active'] ?? false);
        Rate::create($validated);
        return redirect()->route('rates.index')->with('status','created');
    }

    public function edit(Rate $rate): View
    {
        $locations = Location::orderBy('city')->get();
        $services = Service::where('is_active', 1)->orderBy('name')->get();
        $serviceOptions = $this->serviceOptionsFrom($services);
        return view('rates.edit', compact('rate','locations','services','serviceOptions'));
    }

    private function serviceOptionsFrom($services): array
    {
        $allowed = ['Express','Regular','Udara','Laut','CharterPickup','CharterCDD','CharterLongbed','CharterTronton','Free'];
        $codeToEnum = [
            'REG' => 'Regular',
            'EXP' => 'Express',
            'AIR' => 'Udara',
            'SEA' => 'Laut',
            'CHARTER_PICKUP' => 'CharterPickup',
            'CHARTER_CDD' => 'CharterCDD',
            'CHARTER_LONGBED' => 'CharterLongbed',
            'CHARTER_TRONTON' => 'CharterTronton',
            // legacy typos
            'CARTER_PICKUP' => 'CharterPickup',
            'CARTER_CDD' => 'CharterCDD',
            'CARTER_LONGBED' => 'CharterLongbed',
            'CARTER_TRONTON' => 'CharterTronton',
        ];
        $out = [];
        foreach ($services as $svc) {
            $val = $codeToEnum[$svc->code] ?? $svc->name;
            if (!in_array($val, $allowed, true)) continue;
            $label = $svc->name . ($svc->code ? ' ('.$svc->code.')' : '');
            $out[$val] = ['value' => $val, 'label' => $label];
        }
        // ensure unique by value
        return array_values($out);
    }

    public function update(Request $request, Rate $rate): RedirectResponse
    {
        $validated = $request->validate([
            'origin_id' => ['required','integer','exists:locations,id'],
            'destination_id' => ['required','integer','exists:locations,id','different:origin_id'],
            'service_type' => ['required','string','max:100'],
            'price' => ['required','numeric','min:0'],
            'lead_time' => ['nullable','string','max:50'],
            'min_weight' => ['nullable','integer','min:0'],
            'max_weight' => ['nullable','integer','min:0'],
            'is_active' => ['nullable','boolean'],
        ]);

        $dup = Rate::where('origin_id',$validated['origin_id'])
            ->where('destination_id',$validated['destination_id'])
            ->where('service_type',$validated['service_type'])
            ->where('id','<>',$rate->id)
            ->exists();
        if ($dup) {
            return back()->withInput()->withErrors(['service_type' => 'Rate with same origin, destination and service already exists.']);
        }

        $validated['is_active'] = (bool)($validated['is_active'] ?? false);
        $rate->update($validated);
        return redirect()->route('rates.index')->with('status','updated');
    }

    public function destroy(Rate $rate): RedirectResponse
    {
        // allow delete; in real-world, consider archiving instead
        $rate->delete();
        return redirect()->route('rates.index')->with('status','deleted');
    }

    public function options(\Illuminate\Http\Request $request)
    {
        $originId = $request->integer('origin_id');
        $destinationId = $request->integer('destination_id');
        $service = trim((string) $request->get('service_type', ''));

        $rates = Rate::with(['origin','destination'])
            ->when($originId, fn($q) => $q->where('origin_id', $originId))
            ->when($destinationId, fn($q) => $q->where('destination_id', $destinationId))
            ->when($service !== '', fn($q) => $q->where('service_type', 'like', "%$service%"))
            ->orderBy('service_type')
            ->limit(200)
            ->get();

        return response()->json([
            'items' => $rates->map(function($r){
                return [
                    'id' => $r->id,
                    'text' => sprintf('%s → %s | %s | %s',
                        $r->origin?->city,
                        $r->destination?->city,
                        $r->service_type,
                        number_format((float)$r->price, 2)
                    ),
                    'price' => (float) $r->price,
                    'service_type' => $r->service_type,
                ];
            }),
        ]);
    }

    public function export(Request $request): Response
    {
        $rates = Rate::with(['origin','destination'])->orderBy('id')->get();
        $headers = ['origin_id','origin','destination_id','destination','service_type','price','lead_time','min_weight','max_weight','is_active'];
        $lines = [];
        $lines[] = implode(',', $headers);
        foreach ($rates as $r) {
            $row = [
                $r->origin_id,
                self::csvSafe($r->origin?->city.' / '.$r->origin?->province.' / '.$r->origin?->country),
                $r->destination_id,
                self::csvSafe($r->destination?->city.' / '.$r->destination?->province.' / '.$r->destination?->country),
                self::csvSafe($r->service_type),
                $r->price,
                self::csvSafe($r->lead_time),
                (int)($r->min_weight ?? 0),
                (int)($r->max_weight ?? 0),
                $r->is_active ? 1 : 0,
            ];
            $lines[] = implode(',', $row);
        }
        $content = implode("\n", $lines) . "\n";
        $filename = 'rates-export-'.now()->format('Ymd_His').'.csv';
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required','file','mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $created = 0; $updated = 0; $errors = 0; $lineNo = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = null; $map = [];
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                $lineNo++;
                if ($lineNo === 1) {
                    $header = $data;
                    foreach ($header as $i => $col) {
                        $map[Str::lower(trim($col))] = $i;
                    }
                    continue;
                }
                try {
                    $originId = (int) ($data[$map['origin_id']] ?? 0);
                    $destinationId = (int) ($data[$map['destination_id']] ?? 0);
                    $service = trim((string) ($data[$map['service_type']] ?? ''));
                    $price = (float) ($data[$map['price']] ?? 0);
                    $leadTime = trim((string) ($data[$map['lead_time']] ?? ''));
                    $minWeight = isset($map['min_weight']) ? (int)($data[$map['min_weight']] ?? 0) : null;
                    $maxWeight = isset($map['max_weight']) ? (int)($data[$map['max_weight']] ?? 0) : null;
                    $isActive = (int) ($data[$map['is_active']] ?? 1) ? 1 : 0;

                    if (!$originId || !$destinationId || $service === '') {
                        $errors++; continue;
                    }
                    // validate locations
                    if (!Location::whereKey($originId)->exists() || !Location::whereKey($destinationId)->exists()) {
                        $errors++; continue;
                    }

                    $values = [
                        'price' => $price,
                        'lead_time' => $leadTime,
                        'is_active' => $isActive,
                        'min_weight' => $minWeight,
                        'max_weight' => $maxWeight,
                    ];
                    $rate = Rate::updateOrCreate([
                        'origin_id' => $originId,
                        'destination_id' => $destinationId,
                        'service_type' => $service,
                    ], $values);
                    if ($rate->wasRecentlyCreated) { $created++; } else { $updated++; }
                } catch (\Throwable $e) {
                    $errors++;
                }
            }
            fclose($handle);
        }

        $msg = "Imported. Created: $created, Updated: $updated, Errors: $errors";
        return redirect()->route('rates.index')->with('status', $msg);
    }

    private static function csvSafe(?string $value): string
    {
        $v = (string)($value ?? '');
        if (str_contains($v, ',') || str_contains($v, '"')) {
            $v = '"'.str_replace('"', '""', $v).'"';
        }
        return $v;
    }
}
