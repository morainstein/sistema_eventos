<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PagguePaymentService
{
    private PendingRequest $httpRequest;

    public function __construct()
    {
        $this->httpRequest = Http::withHeaders($this->defaultHeaders());
    }

    public function body(array $body): PagguePaymentService
    {
        $signatureHash = hash_hmac('sha256', json_encode($body), env('PAGGUE_CLIENT_SECRET'));

        $this->httpRequest->withBody($body)->withHeader('Signature',$signatureHash);

        return $this;
    }

    public function addHeaders(array $headers): PagguePaymentService
    {
        foreach($headers as $key => $value){
            $this->httpRequest->withHeader($key,$value);
        }

        return $this;
    }

    public function get($url, $query = null): Response
    {
        return $this->httpRequest->get($url, $query);
    }

    public function post($url): Response
    {
        return $this->httpRequest->post($url);
    }

    private function defaultHeaders(): array
    {
        return [
            'X-Company-ID' => env('PAGGUE_COMPANY_ID'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('PAGGUE_BEARER_TOKEN'),
        ];
    }

}