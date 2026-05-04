<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 
        'property_id', 
        'title', 
        'description', 
        'priority', 
        'status', 
        'cost', 
        'scheduled_date',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Scope: only active (not archived)
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    // Scope: only archived
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
}