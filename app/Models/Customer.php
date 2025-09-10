<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Builder;

class Customer extends User
{
    protected $attributes = [
        'role' => UserRoleEnum::CUSTOMER->value,
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', UserRoleEnum::CUSTOMER->value);
        });
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'user_id');
    }
}
