<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthCustomerTest extends TestCase
{
    public function test_deny_when_customer_isnt_authenticated(): void
    {
        $this->getJson('/api/customer')
            ->assertExactJson(['message' => 'Unauthorized'])
            ->assertUnauthorized();
    }
}
