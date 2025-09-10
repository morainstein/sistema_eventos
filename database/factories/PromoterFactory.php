<?php

namespace Database\Factories;

use App\Enums\UserRoleEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promoter>
 */
class PromoterFactory extends UserFactory
{
    protected $role = UserRoleEnum::PROMOTER->value;

}
