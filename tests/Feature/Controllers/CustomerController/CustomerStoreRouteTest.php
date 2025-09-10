<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerStoreRouteTest extends TestCase
{
    public function test_is_really_creating__return_200_ok_and_a_message(): void
    {
        # ARRANGE
        $faker = fake('pt_BR');

        $name = $faker->name();
        $phone = $faker->phoneNumber();
        $email = $faker->email();

        $body = [
            'name' => $name,
            'registry' => $faker->regexify('/[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}/'),
            'phone' => $phone,
            'email' => $email,
            'password' => $faker->password(8), 
        ];

        # ACT
        $response = $this->postJson('/api/customer',$body);

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Customer registered successfully'])
            ->assertCreated();

        $this->assertDatabaseHas('users',[
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
        ]);
    }
}
