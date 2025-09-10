<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthPromoter;
use App\Http\Middleware\VerifyIfPaymentCredentialsExists;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\TestHelper;

class EventShowRouteTest extends TestCase
{
    public function test_show_route_is_returning_in_the_right_format()
    {
        # ARRANGE
        $event = Event::factory()
            ->has(Batch::factory())
            ->for(Promoter::factory())
            ->create();

        $expectedStructure = TestHelper::expectedEventStructure();
        
        # ACT
        $response = $this->getJson("/api/events/{$event->id}");

        # ASSERT

        $response
            ->assertJsonStructure($expectedStructure)
            ->assertOk();
    }
}
