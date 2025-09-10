<?php

namespace Tests\Feature;

use App\Enums\DiscountType;
use App\Enums\PaggueLinks;
use App\Http\Middleware\AuthCustomer;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Event;
use App\Models\PaggueCredentials;
use App\Models\Promoter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerBuyTicketRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuthCustomer::class);
        
        $customer = Customer::factory()->create();
        Auth::login($customer);

        Http::fake(Http::response(['payment' => 'pix_key_example']));
    }

    public function test_cupons_doesnt_exist_returns_404_not_found_and_a_message(): void
    {
        # ARRANGE
        $batch = Batch::factory()->for(Event::factory()->for(Promoter::factory()))->create();

        $body = [
            "coupon" => 'COUPON-EXAMPLE',
        ];
        # ACT
        $response = $this->postJson("/api/batch/{$batch->id}/ticket",$body);

        # ASSERT
        $response
            ->assertExactJson(['message' => 'Coupon does not exists'])
            ->assertNotFound();
    }

    public function test_without_cupon_return_201_created__pix_key_and_payment_amount(): void
    {
        # ARRANGE
        $price = fake('pt_BR')->numberBetween(100,30000);
        $batch = Batch::factory()
            ->for(Event::factory()
                ->for(Promoter::factory()
                    ->has(PaggueCredentials::factory(),'credentials')))
            ->create(['price' => $price]);

        # ACT
        $response = $this->postJson("/api/batch/{$batch->id}/ticket");

        # ASSERT
        $expectedData = [
            'amount' => $price,
            'pix_key' => 'pix_key_example',
        ];

        $response
            ->assertExactJson($expectedData)
            ->assertCreated();
    }

    public function test_its_applying_fixed_discount(): void
    {
        # ARRANGE
        $coupon = 'FIXED15OFF';
        $price = fake('pt_BR')->numberBetween(2000,30000);
        $discountAmount = 1500;
        $finalPrice = $price - $discountAmount;

        $batch = Batch::factory()
            ->for(Event::factory()
                ->for(Promoter::factory()
                    ->has(PaggueCredentials::factory(),'credentials')))
            ->create(['price' => $price]);

        Discount::factory()->create([
            'event_id' => $batch->event->id,
            'coupon_code' => $coupon,
            'discount_type' => DiscountType::FIXED->value,
            'discount_amount' => $discountAmount
        ]);

        # ACT
        $response = $this->postJson("/api/batch/{$batch->id}/ticket",[
            "coupon" => $coupon,
        ]);

        # ASSERT
        $expectedData = [
            'amount' => $finalPrice,
            'pix_key' => 'pix_key_example',
        ];

        $response
            ->assertExactJson($expectedData)
            ->assertCreated();
    }

    public function test_its_applying_percentage_discount(): void
    {
        # ARRANGE
        $coupon = 'PERCENTAGE15OFF';
        $price = fake('pt_BR')->numberBetween(20,300);
        $discountAmount = 10;
        $finalPrice = (int) round($price - ($price * $discountAmount / 100));

        $batch = Batch::factory()
            ->for(Event::factory()
                ->for(Promoter::factory()
                    ->has(PaggueCredentials::factory(),'credentials')))
            ->create(['price' => $price]);

        Discount::factory()->create([
            'event_id' => $batch->event->id,
            'coupon_code' => $coupon,
            'discount_type' => DiscountType::PERCENTAGE->value,
            'discount_amount' => $discountAmount
        ]);

        # ACT
        $response = $this->postJson("/api/batch/{$batch->id}/ticket",[
            "coupon" => $coupon,
        ]);

        # ASSERT
        $expectedData = [
            'amount' => $finalPrice,
            'pix_key' => 'pix_key_example',
        ];

        $response
            ->assertExactJson($expectedData)
            ->assertCreated();
    }
}
