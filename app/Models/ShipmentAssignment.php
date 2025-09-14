<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentAssignment extends Model
{
    use HasFactory;

    protected $fillable = ['shipment_id','driver_id','vehicle_id','forwarder_id','assigned_at'];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function forwarder()
    {
        return $this->belongsTo(Forwarder::class);
    }
}

