<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Batch>
 */
class BatchFactory extends Factory
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
            'event_id' => Event::class,
            'batch' => 1,
            'price' => $faker->numberBetween(1000,30000),
            'tickets_qty' => $faker->numberBetween(10,100),
            'tickets_sold' => 0,
            'end_dateTime' => $faker->date('Y-m-d H:i'),
        ];
    }
}
