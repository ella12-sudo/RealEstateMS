<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    protected $fillable = [
        'tenant_id',
        'property_id',
        'start_date',
        'end_date',
        'monthly_rent',
        'deposit',
        'status',
        'notes',
    ];

    // Lease belongs to a tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Lease belongs to a property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Lease can have many payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}