<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = []; 

    // This is the important part!
    protected $casts = [
        'payment_date' => 'datetime', 
    ];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function property() { return $this->belongsTo(Property::class); }
}