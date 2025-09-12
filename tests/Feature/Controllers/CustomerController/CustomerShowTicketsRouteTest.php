<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthCustomer;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Promoter;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomerShowTicketsRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthCustomer::class);
    }

    public function test_return_200_ok_and_the_right_json_structure(): void
    {
        # ARRANGE
        $customer = Customer::factory()->create();

        Event::factory(3)->for(Promoter::factory())
            ->has(Batch::factory()
                ->has(Ticket::factory()
                    ->state(fn($atr, Batch $b)=>[
                        'event_id' => $b->event_id,
                        'user_id' => $customer->id,
                        'final_price' => $b->price
                    ])))
            ->create();

        Auth::login($customer);
        
        # ACT
        $response = $this->getJson('/api/customer/tickets');
        
        # ASSERT
        $expectedStructure = [
            '*' => [
                "id",
                "batch_id",
                "user_id",
                "payment_status",
                "final_price",
                "created_at",
                "updated_at",
                "event_id",
            ]
        ];

        $response
            ->assertJsonStructure()
            ->assertJsonIsArray()
            ->assertOk();
    }
}
