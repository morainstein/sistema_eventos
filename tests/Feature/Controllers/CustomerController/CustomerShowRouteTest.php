<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthCustomer;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomerShowRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthCustomer::class);
    }

    public function test_show_route_is_returning_200_ok_and_the_customer(): void
    {
        # ARRANGE
        $customer = Customer::factory()->create();
        Auth::login($customer);

        $expectedJson = [
            "id" => $customer->id,
            "phone" => $customer->phone,
            "registry" => $customer->registry,
            "name" => $customer->name,
            "email" => $customer->email,
            "created_at" => $customer->created_at,
        ];

        # ACT
        $response = $this->getJson('/api/customer');


        # ASSERT
        $response
            ->assertExactJson($expectedJson)
            ->assertStatus(200);
    }
}
