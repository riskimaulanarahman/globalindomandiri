<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id','item_type','rate_id','origin_id','destination_id','service_type','min_weight','lead_time','remarks',
        'description','qty','uom','unit_price','amount'
    ];

    public function quotation() { return $this->belongsTo(Quotation::class); }
    public function rate() { return $this->belongsTo(Rate::class); }
    public function origin() { return $this->belongsTo(Location::class, 'origin_id'); }
    public function destination() { return $this->belongsTo(Location::class, 'destination_id'); }
}
