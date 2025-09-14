@extends('layouts.app')

@section('title','Dashboard')

@section('content')
    @php
        $from = request('from') ?: now()->subDays(30)->toDateString();
        $to = request('to') ?: now()->toDateString();
        // KPIs
        $rev = \App\Models\Invoice::when($from, fn($q)=>$q->whereDate('invoice_date','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('invoice_date','<=',$to))
                ->sum('total_amount');
        $arOutstanding = (float)\App\Models\Invoice::sum('total_amount') - (float)\App\Models\Payment::sum('paid_amount');
        $shipTotal = \App\Models\Shipment::when($from, fn($q)=>$q->whereDate('departed_at','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('departed_at','<=',$to))
                ->count();
        $shipDelivered = \App\Models\Shipment::when($from, fn($q)=>$q->whereDate('received_at','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('received_at','<=',$to))
                ->where('status','Delivered')->count();
        $qtnTotal = \App\Models\Quotation::when($from, fn($q)=>$q->whereDate('quote_date','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('quote_date','<=',$to))
                ->count();
        $qtnAccepted = \App\Models\Quotation::where('status','Accepted')
                ->when($from, fn($q)=>$q->whereDate('quote_date','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('quote_date','<=',$to))
                ->count();
        $conv = $qtnTotal ? round($qtnAccepted/$qtnTotal*100) : 0;

        // Funnel data
        $funnelStatuses = ['Draft','Sent','Accepted','Rejected','Converted'];
        $funnelCounts = [];
        foreach ($funnelStatuses as $st) {
            $funnelCounts[] = \App\Models\Quotation::where('status',$st)
                ->when($from, fn($q)=>$q->whereDate('quote_date','>=',$from))
                ->when($to, fn($q)=>$q->whereDate('quote_date','<=',$to))
                ->count();
        }

        // Shipments trend per day
        $period = \Carbon\CarbonPeriod::create($from, $to);
        $trendLabels = [];$trendCounts=[];
        foreach ($period as $p) { $trendLabels[] = $p->format('Y-m-d'); $trendCounts[$p->format('Y-m-d')] = 0; }
        $ships = \App\Models\Shipment::when($from, fn($q)=>$q->whereDate('departed_at','>=',$from))
                    ->when($to, fn($q)=>$q->whereDate('departed_at','<=',$to))
                    ->get(['departed_at']);
        foreach ($ships as $s) { if ($s->departed_at) { $trendCounts[$s->departed_at->format('Y-m-d')] = ($trendCounts[$s->departed_at->format('Y-m-d')] ?? 0)+1; } }
        $trendValues = array_values($trendCounts);

        // AR Aging buckets
        $agingBuckets = ['Not Due'=>0,'0-30'=>0,'31-60'=>0,'61-90'=>0,'91+'=>0];
        $invoicesAll = \App\Models\Invoice::with('payments')->get();
        foreach ($invoicesAll as $inv) {
            $out = (float)$inv->outstanding; if ($out <= 0) continue;
            $due = $inv->due_date ?: $inv->invoice_date; if (!$due) { $agingBuckets['Not Due'] += $out; continue; }
            $dd = \Carbon\Carbon::parse($due);
            if ($dd->isFuture()) { $agingBuckets['Not Due'] += $out; continue; }
            $days = $dd->diffInDays(now());
            if ($days <= 30) $agingBuckets['0-30'] += $out;
            elseif ($days <= 60) $agingBuckets['31-60'] += $out;
            elseif ($days <= 90) $agingBuckets['61-90'] += $out;
            else $agingBuckets['91+'] += $out;
        }
    @endphp

    <div class="d-sm-flex align-items-center justify-content-between mb-3">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <form method="get" class="form-inline">
            <div class="form-row align-items-end">
                <div class="col-auto">
                    <label class="mb-0 small">From</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm ml-2">
                </div>
                <div class="col-auto">
                    <label class="mb-0 small">To</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm ml-2">
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-primary ml-2" type="submit">Apply</button>
                </div>
            </div>
        </form>
    </div>

    <style>
      /* Scoped dashboard styles */
      .kpi .card { border: none; border-radius: 14px; color:#fff; }
      .kpi .icon { opacity:.9; }
      .kpi-primary { background: linear-gradient(135deg, #3B82F6, #06B6D4); }
      .kpi-warning { background: linear-gradient(135deg, #F59E0B, #FBBF24); }
      .kpi-info { background: linear-gradient(135deg, #06B6D4, #10B981); }
      .kpi-success { background: linear-gradient(135deg, #22C55E, #16A34A); }
      .kpi .text-muted, .kpi .text-gray-800 { color:#fff !important; opacity:.95; }
      .kpi .label { font-size: .72rem; letter-spacing: .3px; text-transform: uppercase; opacity:.9; }
    </style>

    <div class="row kpi">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="label font-weight-bold mb-1">Revenue ({{ $from }} → {{ $to }})</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($rev,2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-cash-register fa-2x icon"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="label font-weight-bold mb-1">AR Outstanding</div>
                            <div class="h5 mb-0 font-weight-bold">{{ number_format($arOutstanding,2) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-coins fa-2x icon"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="label font-weight-bold mb-1">Shipments (Total/Delivered)</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $shipTotal }} / {{ $shipDelivered }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-truck fa-2x icon"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card kpi-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="label font-weight-bold mb-1">Conversion Rate</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $conv }}%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-chart-line fa-2x icon"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    @php
        // Service mix (by shipments service_type in period)
        $mix = [];
        $shipMix = \App\Models\Shipment::when($from, fn($q)=>$q->whereDate('departed_at','>=',$from))
                    ->when($to, fn($q)=>$q->whereDate('departed_at','<=',$to))
                    ->get(['service_type']);
        foreach($shipMix as $sm){ $t = $sm->service_type ?: 'Unknown'; $mix[$t] = ($mix[$t] ?? 0) + 1; }
        $mixLabels = array_keys($mix);
        $mixValues = array_values($mix);

        // Top customers by revenue (in period)
        $rows = \App\Models\Invoice::with('customer')
            ->when($from, fn($q)=>$q->whereDate('invoice_date','>=',$from))
            ->when($to, fn($q)=>$q->whereDate('invoice_date','<=',$to))
            ->get();
        $sumByCust = [];$nameByCust=[];
        foreach($rows as $r){ $cid=(int)$r->customer_id; $sumByCust[$cid]=($sumByCust[$cid]??0)+(float)$r->total_amount; $nameByCust[$cid]=$r->customer?->name ?? ('#'.$cid); }
        arsort($sumByCust); $top = array_slice($sumByCust,0,8,true);

        // Alerts
        $overdues = [];
        foreach(\App\Models\Invoice::with('customer','payments')->get() as $inv){
            $out = (float)$inv->outstanding; $due = $inv->due_date ?: $inv->invoice_date;
            if ($out>0 && $due && \Carbon\Carbon::parse($due)->isPast()){
                $days = \Carbon\Carbon::parse($due)->diffInDays(now());
                $overdues[] = ['no'=>$inv->invoice_no,'cust'=>$inv->customer?->name,'due'=>$due,'out'=>$out,'days'=>$days];
            }
        }
        usort($overdues, fn($a,$b)=> $b['days'] <=> $a['days']);
        $overdues = array_slice($overdues,0,8);

        $expiring = \App\Models\Quotation::with('customer')
            ->whereIn('status',['Draft','Sent'])
            ->whereDate('valid_until','<=', now()->addDays(7))
            ->orderBy('valid_until')
            ->limit(8)
            ->get();
    @endphp

    

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Top Customers (Revenue)</h6></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="thead-light"><tr><th>Customer</th><th class="text-right">Amount</th></tr></thead>
                            <tbody>
                            @forelse($top as $cid=>$amt)
                                <tr><td>{{ $nameByCust[$cid] ?? ('#'.$cid) }}</td><td class="text-right">{{ number_format($amt,2) }}</td></tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">No data</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Alerts</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="font-weight-bold mb-2">Overdue Invoices</div>
                            <ul class="list-unstyled mb-3" style="max-height:180px; overflow:auto;">
                                @forelse($overdues as $od)
                                    <li class="mb-2">
                                        <span class="badge badge-danger mr-1">{{ $od['days'] }}d</span>
                                        <strong>{{ $od['no'] }}</strong> • {{ $od['cust'] ?? '-' }}
                                        <div class="small text-muted">Due {{ $od['due'] }} • {{ number_format($od['out'],2) }}</div>
                                    </li>
                                @empty
                                    <li class="text-muted">No overdue</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="font-weight-bold mb-2">Quotes Expiring ≤ 7d</div>
                            <ul class="list-unstyled mb-3" style="max-height:180px; overflow:auto;">
                                @forelse($expiring as $q)
                                    <li class="mb-2">
                                        <span class="badge badge-warning mr-1">{{ $q->valid_until?->diffInDays(now(), false) < 0 ? abs($q->valid_until->diffInDays(now())) . 'd' : 'due' }}</span>
                                        <strong>{{ $q->quote_no }}</strong> • {{ $q->customer?->name ?? '-' }}
                                        <div class="small text-muted">Valid Until {{ $q->valid_until?->format('Y-m-d') }}</div>
                                    </li>
                                @empty
                                    <li class="text-muted">No expiring quotes</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts at the bottom -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Sales Funnel (Quotations)</h6></div>
                <div class="card-body"><canvas id="chartFunnel" height="160"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Shipments Trend</h6></div>
                <div class="card-body"><canvas id="chartTrend" height="160"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">AR Aging</h6></div>
                <div class="card-body"><canvas id="chartAging" height="160"></canvas></div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Service Mix (Shipments)</h6></div>
                <div class="card-body"><canvas id="chartMix" height="160"></canvas></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
      (function(){
        const ctxF = document.getElementById('chartFunnel');
        if (ctxF) new Chart(ctxF, {
          type: 'bar',
          data: { labels: @json($funnelStatuses), datasets: [{
            label: 'Quotations', data: @json($funnelCounts),
            backgroundColor: ['#9CA3AF','#60A5FA','#34D399','#F87171','#FBBF24']
          }]},
          options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks:{ precision:0 } } } }
        });

        const ctxT = document.getElementById('chartTrend');
        if (ctxT) new Chart(ctxT, {
          type: 'line',
          data: { labels: @json($trendLabels), datasets: [{
            label: 'Shipments', data: @json($trendValues), fill:false, borderColor:'#3B82F6', tension:.25, pointRadius:2
          }]},
          options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks:{ precision:0 } } }, plugins:{legend:{display:false}} }
        });

        const ctxA = document.getElementById('chartAging');
        if (ctxA) new Chart(ctxA, {
          type: 'doughnut',
          data: { labels: @json(array_keys($agingBuckets)), datasets: [{
            label: 'AR Aging', data: @json(array_values($agingBuckets)),
            backgroundColor: ['#10B981','#FBBF24','#F59E0B','#EF4444','#7C3AED']
          }]},
          options: { responsive: true, maintainAspectRatio: false }
        });

        const ctxM = document.getElementById('chartMix');
        if (ctxM) new Chart(ctxM, {
          type: 'pie',
          data: { labels: @json($mixLabels), datasets: [{
            data: @json($mixValues),
            backgroundColor: ['#3B82F6','#10B981','#F59E0B','#EF4444','#6366F1','#14B8A6','#A855F7','#F97316','#84CC16','#0EA5E9']
          }]},
          options: { responsive: true, maintainAspectRatio: false }
        });
      })();
    </script>
    @endpush
@endsection
