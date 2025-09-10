<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Models\Batch;
use App\Models\Discount;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DiscountIndexRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
    }

    public function test_returns_200_ok_and_the_right_json_structure(): void
    {
        # ARRANGE
        $event = Event::factory()
            ->for(Promoter::factory())
            ->has(Batch::factory())
            ->has(Discount::factory(2))->create();
            
        # ACT
        $response = $this->getJson("/api/promoter/{$event->id}/discount");
        
        # ASSERT
        $expectedStructure = [
        '*' => [
            "coupon_code",
            "event_id",
            "discount_type",
            "discount_amount",
            "usage_limit",
            "times_used",
            "valid_until",
            ]
        ];

        $response
            ->assertExactJsonStructure($expectedStructure)
            ->assertOk();
    }
}
