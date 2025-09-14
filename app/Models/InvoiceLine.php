<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id','shipment_id','description','qty','uom','amount'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
}
