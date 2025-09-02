<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasUuids;

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
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    
}
