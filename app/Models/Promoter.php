<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Builder;

class Promoter extends User
{
    protected $attributes = [
        'role' => UserRoleEnum::PROMOTER->value,
    ];

    protected $with = [
        'credentials'
    ];

    protected $hidden = [
        'credentials'
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', UserRoleEnum::PROMOTER->value);
        });
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'promoter_id');
    }

    public function credentials()
    {
        return $this->hasOne(PaggueCredentials::class,'promoter_id','id');
    }
}
