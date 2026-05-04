<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'user_id',
        'property_id',
        'emergency_contact',
        'emergency_phone',
        'lease_start',
        'lease_end',
        'status',
    ];

    protected $casts = [
        'lease_start' => 'date',
        'lease_end'   => 'date',
    ];

    // Automatically deletes related records when a tenant is deleted
    protected static function booted()
    {
        static::deleting(function ($tenant) {
            $tenant->payments()->delete();
            $tenant->maintenances()->delete();
            $tenant->leases()->delete();
        });
    }

    // Connects to the User table for login/name
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Connects to the Property table
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Relationship for the Maintenance table.
     * This allows us to call $tenant->maintenances
     */
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    /**
     * Keep this as an alias just in case you used this name elsewhere.
     */
    public function maintenanceRequests()
    {
        return $this->hasMany(Maintenance::class);
    }
}