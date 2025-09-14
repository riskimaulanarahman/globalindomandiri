<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportXlsx extends Command
{
    protected $signature = 'import:xlsx {path : Path to the Excel file}';
    protected $description = 'Import XLSX sheets (Customers, Rates, Shipments, etc) [stub]';

    public function handle(): int
    {
        $path = $this->argument('path');
        // TODO: Implement ImportService to parse sheets and upsert records.
        $this->warn('ImportService not fully implemented yet. Provided path: '.$path);
        return self::SUCCESS;
    }
}

