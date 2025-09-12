<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Ticket extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'event_id',
        'batch_id',
        'user_id',
        'final_price'
    ];

    protected $attributes = [
        'payment_status' => PaymentStatus::PENDING->value,
    ];

    protected function casts(): array
    {
        return [
            'payment_status' => PaymentStatus::class,
        ];
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function promoter() : HasOneThrough
    {
        return $this->through(Event::class)->has(Promoter::class);
    }
}
