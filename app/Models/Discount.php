<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasUuids;

    protected $table = 'discounts';

    protected $fillable = [
        'event_id',
        'coupon_code',
        'discount_type',
        'discount_amount',
        'usage_limit',
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

    protected function discountType() : Attribute
    {
        return Attribute::make(
            set: fn ($value) => strtoupper($value)
        );
    }

    protected function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
