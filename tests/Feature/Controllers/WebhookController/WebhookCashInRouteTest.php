<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Events\TicketPayedEvent;
use App\Http\Middleware\ValidateSignatureCashInPaggue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event as LaravelEvent;
use Tests\TestCase;
use Tests\TestHelper;

class WebhookCashInRouteTest extends TestCase
{
    private array $body;
    private array $headers;
 
    protected function setUp(): void
    {
        parent::setUp();
        [$this->body, $this->headers] = TestHelper::simulateWebhookRequestCenarioAndReturnBodyAndHeaders();
    }
    
    public function test_route_is_dispatching_TicketPayedEvent_and_returning_200_ok(): void
    {
        # ARRANGE
        LaravelEvent::fake();

        # ACT
        $response = $this->postJson(route('paggue.webhook.cash-in'),$this->body,$this->headers);
        
        # ASSERT
        $response->assertOk();
        LaravelEvent::assertDispatched(TicketPayedEvent::class);
    }

    public function test_when_ticket_status_inst_payed_dont_dispatch_TicketPayedEvent(): void
    {
        # ARRANGE
        $this->withoutMiddleware(ValidateSignatureCashInPaggue::class);
        LaravelEvent::fake();
        $body = $this->body;
        $body['status'] = 0;

        # ACT
        $response = $this->postJson(route('paggue.webhook.cash-in'),$body,$this->headers);
        
        # ASSERT
        $response->assertOk();
        LaravelEvent::assertNotDispatched(TicketPayedEvent::class);
    }
    
}
