<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\TestHelper;

class ValidateSignatureCashInPaggueTest extends TestCase
{

    private array $body;
    private array $headers;
 
    protected function setUp(): void
    {
        parent::setUp();
        [$this->body, $this->headers] = TestHelper::simulateWebhookRequestCenarioAndReturnBodyAndHeaders();
    }

    public function test_signature_is_ok_and_cash_in_route_is_returning_200_ok()
    {
        # ACT
        $response = $this->postJson(route('paggue.webhook.cash-in'),$this->body,$this->headers);
        
        # ASSERT

        $response
            ->assertOk();
    }

    public function test_when_signatures_header_doesnt_exist_return_401_unauthorized_and_a_message(): void
    {
        # ARRANGE
        $headers = [];

        # ACT
        $response = $this->postJson(route('paggue.webhook.cash-in'),$this->body,$headers);
        
        # ASSERT

        $response
            ->assertExactJson(['message' => "header 'signature' has not been provided"])
            ->assertUnauthorized();
    }

    public function test_when_signatures_header_is_invalid_return_401_unauthorized_and_a_message(): void
    {
        # ARRANGE
        $headers = [
            'signature' => 'invalid'
        ];

        # ACT
        $response = $this->postJson(route('paggue.webhook.cash-in'),$this->body,$headers);

        # ASSERT

        $response
            ->assertExactJson(['message' => "header 'signature' is invalid"])
            ->assertUnauthorized();
    }
}
