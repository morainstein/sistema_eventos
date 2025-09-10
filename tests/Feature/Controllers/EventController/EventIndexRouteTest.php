<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Http\Middleware\VerifyIfPaymentCredentialsExists;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestHelper;

class EventIndexRouteTest extends TestCase
{
    // use RefreshDatabase;

    public function test_index_route_is_returning_events_in_the_right_format()
    {
        # ARRANGE
        Promoter::factory(3)
            ->has(Event::factory(3)
                ->has(Batch::factory())
            )->create();
            
        $expectedStructure = [
            '*' => TestHelper::expectedEventStructure()
        ];
        # ACT
        $response = $this->getJson('/api/events');

        # ASSERT
        $response
            ->assertJsonIsArray()
            ->assertJsonStructure($expectedStructure)
            ->assertOk();
    }
}
