<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
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
            'batch_id' => Batch::class,
            'user_id' => Customer::class,
            'payment_status'=> PaymentStatus::PENDING->value,
        ];
    }
}
