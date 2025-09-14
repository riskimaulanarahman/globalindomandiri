<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name','code','default_terms','is_active'];

    public function terms()
    {
        return $this->belongsToMany(TermsAndCondition::class, 'service_terms_and_conditions', 'service_id', 'terms_and_condition_id');
    }
}
