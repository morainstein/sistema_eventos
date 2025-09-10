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

class DiscountDestroyRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthPromoter::class);
    }

    public function test_really_delete_discount__return_200_and_a_message(): void
    {
        # ARRANGE
        $event = Event::factory()
            ->for(Promoter::factory())
            ->has(Batch::factory())
            ->create();
        $discount = Discount::factory()->create(['event_id'=> $event->id]);

        # ACT
        $response = $this->deleteJson("/api/promoter/discount/{$discount->id}");

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Discount destroyed successfully'])
            ->assertOk();
        
        $this->assertNull(Discount::where('event_id',$event->id)->first(),
            "Discount wasn't deleted");
    }
}
