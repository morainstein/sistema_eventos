<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'start_dateTime',
        'end_dateTime',
        'banner_link',
    ];

    protected $hidden = [
        'updated_at',
    ];

    protected $attributes = [
        "description" => null,
        "banner_link" => null
    ];

    protected function casts(): array
    {
        return [
            'start_dateTime' => 'datetime:Y-m-d H:i',
            'end_dateTime' => 'datetime:Y-m-d H:i',
        ];
    }

    public function promoter()
    {
        return $this->belongsTo(Promoter::class, 'promoter_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'event_id');
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class, 'event_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }
}
