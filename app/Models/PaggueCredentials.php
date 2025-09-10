<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaggueCredentials extends Model
{
    use HasUuids, HasFactory;

    protected $table = "paggue_credentials";

    protected $fillable = [
        'company_id',
        'webhook_token',
        'bearer_token',
    ];

    public function promoter()
    {
        return $this->belongsTo(Promoter::class,'promoter_id','id');
    }
}
