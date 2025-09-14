<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_no','quote_date','valid_until','status','customer_id','origin_id','destination_id','service_type','service_id','lead_time',
        'currency','tax_pct','discount_amt','subtotal','total','attention','customer_phone','payment_term_id','terms_and_conditions_id','terms_conditions','branch','created_by'
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function origin() { return $this->belongsTo(Location::class, 'origin_id'); }
    public function destination() { return $this->belongsTo(Location::class, 'destination_id'); }
    public function lines() { return $this->hasMany(QuotationLine::class); }
    public function paymentTerm() { return $this->belongsTo(\App\Models\PaymentTerm::class, 'payment_term_id'); }
    public function service() { return $this->belongsTo(\App\Models\Service::class, 'service_id'); }
    public function termsDefinition() { return $this->belongsTo(\App\Models\TermsAndCondition::class, 'terms_and_conditions_id'); }
    public function shipments() { return $this->hasMany(\App\Models\Shipment::class, 'quotation_id'); }
}

