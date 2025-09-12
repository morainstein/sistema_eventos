<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasUuids, HasFactory;

    const FIXED = 'FIXED';
    const PERCENTAGE = 'PERCENTAGE';

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
        'updated_at',
        'id'
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

    #[Scope]
    protected function findByCoupon(Builder $query, string $coupon): void
    {
        $query->where('coupon_code', $coupon);
    }

    #[Scope]
    protected function whereEventId(Builder $query, string $eventId): void
    {
        $query->where('event_id', $eventId);
    }

    #[Scope]
    protected function whereIsValid(Builder $query): void
    {
        $query->where('valid_until', '>', now())
            ->orderBy('discounts.created_at', 'desc');
    }
}
