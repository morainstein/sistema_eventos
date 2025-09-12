<?php

namespace Tests\Unit;

use App\Enums\PaggueLinks;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use App\Models\Ticket;
use App\Services\PagguePaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PagguePaymentServiceTest extends TestCase
{
    // private Promoter $promoter;
    private PaggueCredentials $credentials;

    protected function setUp(): void
    {
        parent::setUp();
        $promoter = Promoter::factory()
            ->has(PaggueCredentials::factory(),'credentials')
            ->create();
        Auth::login($promoter);
        $this->credentials = $promoter->credentials;
    }    

    public function test_send_authorization_and_signature_headeres_in_the_right_format(): void
    {
        # ARRANGE
        Http::fake(Http::response([
            "payment" => "pix_key_123456789",
        ]));
        
        $ticket = new Ticket([
            'id' => 1,
            'final_price' => 5000
        ]);

        # ACT
        $pagguePaymentService = PagguePaymentService::credentials($this->credentials);
        $pagguePaymentService->buyTicketByPixStatic($ticket);

        # ASSERT
        Http::assertSent(function ($request) {
            return $request->url() == PaggueLinks::CREATE_PIX_STATIC->value;
        });

        Http::assertSent(function ($request) {
            return $request->header('Authorization')[0] == 'Bearer ' . $this->credentials->bearer_token;
        });

        Http::assertSent(function ($request) {
            $signatureTest = hash_hmac('sha256', $request->body(), $this->credentials->webhook_token);

            return hash_equals($request->header('Signature')[0], $signatureTest);
        });
    }
}
