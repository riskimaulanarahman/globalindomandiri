<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Console\Command;

class ArMarkOverdue extends Command
{
    protected $signature = 'ar:mark-overdue';
    protected $description = 'Mark invoices as Overdue when past due_date with outstanding balance';

    public function handle(InvoiceService $svc): int
    {
        Invoice::whereNotNull('due_date')->whereDate('due_date','<', now()->toDateString())
            ->whereIn('status', ['Draft','Sent','PartiallyPaid'])
            ->chunkById(200, function ($rows) use ($svc) {
                foreach ($rows as $inv) { $svc->refreshStatus($inv); }
            });
        $this->info('Overdue evaluation complete');
        return self::SUCCESS;
    }
}

