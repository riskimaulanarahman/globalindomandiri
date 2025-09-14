<?php

namespace App\Services;

class ImportService
{
    public function import(string $path): array
    {
        // TODO: parse Excel sheets (Customers, MASTER RATE, Monitoring Resi, Detail Berat, Monitoring Invoice)
        // using maatwebsite/excel and map to DTOs -> upsert into tables; return summary
        return ['status' => 'TODO', 'path' => $path];
    }
}

