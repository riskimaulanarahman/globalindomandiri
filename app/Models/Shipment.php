<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'resi_no','letter_no','quote_no','customer_id','sender_customer_id','receiver_customer_id',
        'sender_contact_id','receiver_contact_id',
        'sender_name','sender_address','receiver_name','receiver_address',
        'sender_pic','sender_phone','receiver_pic','receiver_phone','item_desc','notes',
        'origin_id','destination_id','service_type','shipment_kind','payment_method','weight_charge','weight_actual',
        'volume_weight','koli_count','base_fare','packing_fee','insurance_fee','discount','ppn','pph23','other_fee',
        'total_cost','departed_at','received_at','status','rate_id','sales_owner','sla_on_time'
    ];

    protected $casts = [
        'departed_at' => 'datetime',
        'received_at' => 'datetime',
        'sla_on_time' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function senderCustomer()
    {
        return $this->belongsTo(Customer::class, 'sender_customer_id');
    }

    public function receiverCustomer()
    {
        return $this->belongsTo(Customer::class, 'receiver_customer_id');
    }

    public function senderContact()
    {
        return $this->belongsTo(CustomerContact::class, 'sender_contact_id');
    }

    public function receiverContact()
    {
        return $this->belongsTo(CustomerContact::class, 'receiver_contact_id');
    }

    public function origin()
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination()
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function items()
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function rate()
    {
        return $this->belongsTo(Rate::class);
    }

    public function assignment()
    {
        return $this->hasOne(ShipmentAssignment::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function invoiceLine()
    {
        return $this->hasOne(InvoiceLine::class);
    }
}
