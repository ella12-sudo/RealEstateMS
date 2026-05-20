<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Maintenance extends Model
{
    protected $fillable = [
        'tenant_id',
        'property_id',
        'title',
        'description',
        'priority',
        'status',
        'cost',
        'scheduled_date',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'cost'        => 'decimal:2',
    ];

    public function scopeActive(Builder $query)
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived(Builder $query)
    {
        return $query->whereNotNull('archived_at');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}