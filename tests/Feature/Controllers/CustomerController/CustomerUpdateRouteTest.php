<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthCustomer;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomerUpdateRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthCustomer::class);
    }

    public function test_is_really_updating__returning_200_ok_and_a_message(): void
    {
        # ARRANGE
        $customer = Customer::factory()->create();
        Auth::login($customer);

        $faker = fake('pt_BR');
        
        $name = $faker->name();
        $phone = $faker->phoneNumber();
        $email = $faker->email();

        $body = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password' => $faker->password(8),
        ];

        # ACT
        $response = $this->putJson('/api/customer',$body);

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Customer updated successfully'])
            ->assertOk();

        $this->assertDatabaseHas('users',[
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ]);
    }
}
