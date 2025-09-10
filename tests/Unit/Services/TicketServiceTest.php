<?php

namespace Tests\Unit;

use App\Enums\DiscountType;
use App\Models\Batch;
use App\Models\Discount;
use App\Models\Event;
use App\Models\Promoter;
use App\Services\DiscountService;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    private Batch $batch;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->batch = Batch::factory()
            ->for(Event::factory()->for(Promoter::factory()))
            ->create();

        $this->request = new Request();
        $this->request->batch = $this->batch;
        $this->request->coupon = null;
    }

    public function test_calculate_tickets_final_price_without_discounts(): void
    {
        # ARRANGE

        # ACT
        $ticketsFinalPrice = (new TicketService())->calculateTicketsFinalPrice($this->request);

        # ASSERT
        $this->assertEquals($ticketsFinalPrice, $this->batch->price);
    }

    public function test_calculate_tickets_final_price_with_fixed_discount(): void
    {
        # ARRANGE
        $discountAmount = fake()->numberBetween(5,30) * 100;
        $discount = Discount::factory()->create([
            'event_id' => $this->batch->event->id,
            'discount_type' => DiscountType::FIXED->value,
            'discount_amount' => $discountAmount,
        ]);

        $this->request->coupon = $discount->coupon_code;

        # ACT
        $ticketsFinalPrice = (new TicketService())->calculateTicketsFinalPrice($this->request);

        # ASSERT
        $this->assertEquals($ticketsFinalPrice, ($this->batch->price - $discountAmount));
    }

    public function test_calculate_tickets_final_price_with_percentage_discount(): void
    {
        # ARRANGE
        $discountAmount = fake()->numberBetween(5,30);
        $discount = Discount::factory()->create([
            'event_id' => $this->batch->event->id,
            'discount_type' => DiscountType::PERCENTAGE->value,
            'discount_amount' => $discountAmount,
        ]);

        $this->request->coupon = $discount->coupon_code;

        # ACT
        $ticketsFinalPrice = (new TicketService())->calculateTicketsFinalPrice($this->request);

        # ASSERT
        $expectedPrice = DiscountService::applyPercentageDiscount($this->batch->price, $discountAmount);
        $this->assertEquals($ticketsFinalPrice, $expectedPrice);
    }
}
