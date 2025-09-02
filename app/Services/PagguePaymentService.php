<?php

namespace App\Services;

use App\Enums\PaggueLinks;
use App\Models\PaggueCredentials;
use App\Models\Ticket;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PagguePaymentService
{
    private PendingRequest $httpRequest;
    private PaggueCredentials $promoterCredentials;

    public function __construct(PaggueCredentials $paggueCredentials)
    {
        $this->promoterCredentials = $paggueCredentials;
        $this->httpRequest = Http::withHeaders($this->defaultHeaders());
    }

    static public function credentials(PaggueCredentials $paggueCredentials)
    {
        return new PagguePaymentService($paggueCredentials);
    }

    /**
     * @return string chave pix
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function buyTicketByPixStatic(Ticket $ticket)
    {
        $body = [
            "external_id" => $ticket->id,
            "amount" => ($ticket->final_price * 100),
            "description" => "Pagamento do ingresso #{$ticket->id}",
            "payer_name" => Auth::user()->name
        ];

        $response = $this->body($body)->post(PaggueLinks::CREATE_PIX_STATIC->value);

        return $response->object()->payment;
    }

    /**
     * @return string webhook id 
     */
    public function createPixWebhook() : string
    {
        $body = [
            'type' => 0,
            'url' => env('APP_DOMAIN_NAME') .route('paggue.webhook.cash-in', absolute: false)
        ];

        $response = $this->body($body)->post(PaggueLinks::WEBHOOK_MANAGE_URL->value)->throw();

        return $response->object()->id;

    }

    public function deletePixWebhook()
    {
        $url = PaggueLinks::WEBHOOK_MANAGE_URL->value ."/{$this->promoterCredentials->webhook_id}";
        return $this->body([])->delete($url);
    }

    private function defaultHeaders(): array
    {
        return [
            'X-Company-ID' => $this->promoterCredentials->company_id,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->promoterCredentials->bearer_token,
        ];
    }

    private function body(array $body): PagguePaymentService
    {
        $bodyInJson = json_encode($body);
        $signatureHash = hash_hmac('sha256', $bodyInJson, $this->promoterCredentials->webhook_token);

        $this->httpRequest->withBody($bodyInJson)->withHeader('Signature',$signatureHash);

        return $this;
    }

    private function addHeaders(array $headers): PagguePaymentService
    {
        foreach($headers as $key => $value){
            $this->httpRequest->withHeader($key,$value);
        }

        return $this;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function get($url, $query = null): Response
    {
        return $this->httpRequest->get($url, $query);
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function post($url): Response
    {
        return $this->httpRequest->post($url)->throw();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function delete($url): Response
    {
        return $this->httpRequest->delete($url)->throw();
    }
}