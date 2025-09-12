<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Event;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Admin::factory()->create([
            "name" => "Admin Oliveira",
            "registry" => "111.000.000-00",
            "phone" => "(75)9 1111-0000",
            "email" => "admin@oliveira.com",
            "password" => "12345678"

        ]);

        $promoter = Promoter::factory()->has(PaggueCredentials::factory(),'credentials')
            ->create([
            "name" => "Promotor Silva",
            "registry" => "222.000.000-00",
            "phone" => "(75)9 2222-0000",
            "email" => "promotor@silva.com",
            "password" => "12345678"
        ]);

        $event = Event::factory()->for($promoter)->create([
            'title' => "Dominators",
            'description' => "Campeonato de dominó valendo copo dágua",
        ]);

        Batch::factory()->for($event)->create([
            'price' => 2000,
            'batch' => 1,
        ]);

        Batch::factory()->for($event)->create([
            'price' => 3000,
            'batch' => 2,
        ]);

        Discount::factory()->create([
            'event_id' => $event->id,
            'coupon_code' => "FIXED15-OFF",
            'discount_type' => Discount::FIXED,
            'discount_amount' => 1500,
        ]);

        Discount::factory()->create([
            'event_id' => $event->id,
            'coupon_code' => "PERCENTAGE10-OFF",
            'discount_type' => Discount::PERCENTAGE,
            'discount_amount' => 10,
        ]);

        Customer::factory()->create([
            "name" => "Cliente Santos",
            "registry" => "333.000.000-00",
            "phone" => "(75)9 3333-0000",
            "email" => "cliente@santos.com",
            "password" => "12345678",
        ]);
    }
}
