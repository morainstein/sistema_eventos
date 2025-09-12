<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Models\Batch;
use App\Models\Discount;
use App\Models\Event;
use App\Models\Promoter;
use Database\Factories\DiscountFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiscountStoreRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
    }

    public function test_really_stores_in_database___return_201_created_and_a_message(): void
    {
        # ARRANGE
        $event = Event::factory()
            ->for(Promoter::factory())
            ->has(Batch::factory())
            ->create();
        
        $discountPayload = (new DiscountFactory)->definition();
        $discountPayload['event_id'] = $event->id;

        # ACT
        $response = $this->postJson('/api/promoter/discount',$discountPayload);
        
        # ASSERT
        $response
            ->assertExactJson(['message' => 'Discount registered successfully'])
            ->assertCreated();

        $this->assertDatabaseHas('discounts',$discountPayload);
    }
}
