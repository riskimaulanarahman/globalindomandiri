<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Services\SlaService;
use Illuminate\Console\Command;

class RecalcSla extends Command
{
    protected $signature = 'ops:recalc-sla {--since=7 : Days back to check}';
    protected $description = 'Recalculate SLA on-time flags for recent shipments';

    public function handle(SlaService $svc): int
    {
        $since = now()->subDays((int)$this->option('since'));
        Shipment::whereNotNull('received_at')->where('received_at','>=',$since)
            ->with('rate')->chunkById(200, function ($rows) use ($svc) {
                foreach ($rows as $s) { $svc->evaluate($s); }
            });
        $this->info('SLA recalculation complete');
        return self::SUCCESS;
    }
}

