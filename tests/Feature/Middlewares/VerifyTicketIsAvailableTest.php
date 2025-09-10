<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthCustomer;
use App\Models\Batch;
use App\Models\Event;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VerifyTicketIsAvailableTest extends TestCase
{
    private Batch $batch;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthCustomer::class);

        $this->batch = Batch::factory()
            ->for(Event::factory()->for(Promoter::factory()))
            ->create([
                'tickets_qty' => 10,
                'tickets_sold' => 10
            ]);
    }

    public function test_when_tickets_is_unavailable_return_status_code_410_and_an_informative_message(): void
    {
        $response = $this->postJson("/api/batch/{$this->batch->id}/ticket");

        $response
            ->assertExactJson(['message' => 'No tickets available'])
            ->assertGone();
    }
}
