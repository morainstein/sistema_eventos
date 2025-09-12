<?php

namespace Database\Factories;

use App\Enums\UserRoleEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends UserFactory
{
    protected $role = UserRoleEnum::ADMIN->value;
}
