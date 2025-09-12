<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
abstract class UserFactory extends Factory
{
    protected $role;

    public function definition(): array
    {
        $faker = fake('pt_BR');
        return [
            'role' => $this->role,
            'phone' => $faker->phoneNumber(),
            'registry' => $faker->regexify('/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/'),
            'name' => $faker->name(),
            'email' => $faker->email(),
            // 'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
