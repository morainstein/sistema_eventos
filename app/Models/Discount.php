<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';

    protected $fillable = [
        'coupon_code',
        'discount_type',
        'discount_amount',
        'usage_limit',
        'times_used',
        'valid_until'
    ];

    protected $casts = [
        'discount_type' => 'string',
        'valid_until' => 'datetime',
    ];

    protected $attributes = [
        'times_used' => 0,
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
