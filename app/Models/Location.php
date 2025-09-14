<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['city','province','country'];

    public function originRates()
    {
        return $this->hasMany(Rate::class, 'origin_id');
    }

    public function destinationRates()
    {
        return $this->hasMany(Rate::class, 'destination_id');
    }
}

