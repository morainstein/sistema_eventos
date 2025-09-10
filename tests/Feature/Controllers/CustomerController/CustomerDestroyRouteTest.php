<?php

namespace Tests\Feature;

use App\Http\Middleware\AuthCustomer;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CustomerDestroyRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthCustomer::class);
    }

    public function test_is_really_soft_deleting__returning_200_ok(): void
    {
        # ARRANGE
        $customer = Customer::factory()->create();
        Auth::login($customer);

        # ACT
        $response = $this->deleteJson('/api/customer');

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Customer has been soft deleted'])
            ->assertOk();
        
        $this->assertNull(Customer::find($customer->id));
        
        $this->assertInstanceOf(Customer::class,Customer::withTrashed()->find($customer->id),"User wasn't found as soft deleted");
    }

}
