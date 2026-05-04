<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRequest extends Model
{
    protected $fillable = [
        'tenant_id',
        'property_id',
        'issue',
        'category',
        'priority',
        'status',
        'description',
        'resolved_date',
    ];

    // MaintenanceRequest belongs to a tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // MaintenanceRequest belongs to a property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}