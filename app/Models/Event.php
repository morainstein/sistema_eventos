<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasUuids;

    protected $table = 'events';

    protected $fillable = [
        'promoter_id',
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

    public function promoter()
    {
        return $this->belongsTo(Promoter::class, 'promoter_id');
    }

    public function batches()
    {
        return $this->hasMany(Batch::class, 'event_id');
    }
}
