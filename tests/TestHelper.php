<?php

namespace Tests;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\Event;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use App\Models\Ticket;
use App\Services\TicketService;

class TestHelper
{
    static public function generateDataToCreateAnEventModel(string $promoterId): array
    {
        $faker = fake('pt_BR');

        return [
            "promoter_id" => $promoterId,
            "title" => $faker->title(),
            "description" => $faker->paragraph(),
            "start_dateTime" => $faker->date('Y-m-d H:i'),
            "end_dateTime" => $faker->date('Y-m-d H:i'),
            "batches" => [
                [
                    "price" => $faker->numerify('###'),
                    "tickets_qty" => $faker->numberBetween(1,150),
                    "end_dateTime" => $faker->date('Y-m-d H:i'),
                ],
                [
                    "price" => $faker->numerify('###'),
                    "tickets_qty" => $faker->numberBetween(1,150),
                    "end_dateTime" => $faker->date('Y-m-d H:i'),
                ]
            ],
        ];
    }

    static public function expectedEventStructure(): array
    {
        return [
            'id',
            'promoter_id',
            'title',
            'description',
            'start_dateTime',
            'end_dateTime', 
            'batches' => [
                [
                    'id',
                    'batch',
                    'price',
                    'tickets_qty',
                    'tickets_sold',
                    'end_dateTime',
                ]
            ]
        ];
    }

    /**
     * @return array [$body, $headers] simulating a Paggue's cash-in webhook request
     */
    static public function simulateWebhookRequestCenarioAndReturnBodyAndHeaders() : array
    {
        $batch = Batch::factory()
            ->for(Event::factory()
                ->for(Promoter::factory()->has(PaggueCredentials::factory(),'credentials')))
            ->create();
        $customer = Customer::factory()
            ->create();
        $ticket = Ticket::factory()->create([
                'event_id' => $batch->event_id,
                'batch_id' => $batch->id,
                'user_id' => $customer->id,
                'final_price' => $batch->price,
            ]);

        $promoterCredentials = Promoter::findPromoterByTicketsId($ticket->id)
            ->credentials;

        $body = [
            'external_id' => $ticket->id,
            'payer_name' => $customer->name,
            'amount' => $ticket->final_price,
            'status' => 1
        ];

        $headers = [
            'signature' => hash_hmac('sha256',json_encode($body),$promoterCredentials->webhook_token),
        ];

        return [$body, $headers];
    }
}
