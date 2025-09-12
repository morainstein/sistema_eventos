<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthAdminTest extends TestCase
{
    public function test_deny_when_admin_isnt_authenticated(): void
    {
        $this->getJson('/api/admin')
            ->assertExactJson(['message' => 'Unauthorized'])
            ->assertUnauthorized();
    }
}
