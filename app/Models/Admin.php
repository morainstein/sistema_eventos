<?php

namespace App\Models;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Builder;

class Admin extends User
{
    protected $attributes = [
        'role' => UserRoleEnum::ADMIN->value,
    ];

    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', UserRoleEnum::ADMIN->value);
        });
    }
}
