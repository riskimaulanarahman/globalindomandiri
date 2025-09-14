<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_no','po_no','invoice_date','customer_id','top_days','terms_text','received_date','due_date','total_amount','status','remarks'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'received_date' => 'date',
        'due_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->sum('paid_amount');
    }

    public function getOutstandingAttribute(): float
    {
        return max(0.0, (float)($this->total_amount ?? 0) - $this->paid_amount);
    }
}
