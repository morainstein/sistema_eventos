<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'batches';

    protected $fillable = [
        'price',
        'tickets_qty',
        'end_dateTime'
    ];

    protected $with = [
        'event'
    ];

    protected $hidden = [
        'event_id',
        'updated_at',
        'event'
    ];

    protected function casts(): array
    {
        return [
            'start_dateTime' => 'datetime:Y-m-d H:i',
            'end_dateTime' => 'datetime:Y-m-d H:i',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'batch_id');
    }
}
