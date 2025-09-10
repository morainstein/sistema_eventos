<?php

namespace Database\Factories;

use App\Models\Promoter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaggueCredentials>
 */
class PaggueCredentialsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'promoter_id' => Promoter::class,
            'company_id' => '12345',
            'webhook_id' => '001',
            'webhook_token' => static::webhookTokenExample(),
            'bearer_token' => static::bearerTokenExample(),
        ];
    }

    static public function webhookTokenExample(): string
    {
        return hash_hmac('sha256','webhook_token','webhook_token');
    }

    static public function bearerTokenExample(): string
    {
        return hash_hmac('sha256','bearer_token','bearer_token');
    }
}
