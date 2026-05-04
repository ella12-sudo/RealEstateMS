<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'payment_id',
        'amount',
        'net_amount',
        'vat_amount',
        'method',
        'status',
    ];
}