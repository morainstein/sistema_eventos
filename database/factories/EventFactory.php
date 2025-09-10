<?php

namespace Database\Factories;

use App\Models\Promoter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('pt_BR');
        return [
            'promoter_id' => Promoter::class,
            'title' => $faker->word(),
            'description' => $faker->paragraph(),
            'start_dateTime' => $faker->date('Y-m-d H:i'),
            'end_dateTime' => $faker->date('Y-m-d H:i'),
            'banner_link' => null,
        ];
    }
}
