<?php

namespace Database\Factories;

use App\Enums\DiscountType;
use App\Models\Discount;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discount>
 */
class DiscountFactory extends Factory
{
    public function definition(): array
    {
        $faker = fake('pt_BR');
        $discountType = $faker->randomElement([
            Discount::FIXED,Discount::PERCENTAGE
        ]);

        $discountAmount = $faker->numberBetween(5,30);
        $discountAmount = $discountType == Discount::FIXED ? $discountAmount * 100 : $discountAmount;
        return [
            'event_id' => Event::class,
            'coupon_code' => Str::upper($faker->word()),
            'discount_type' => $discountType,
            'discount_amount' => $discountAmount,
            'usage_limit' => $faker->numberBetween(1,10),
            'times_used' => 0,
            'valid_until' => $faker->dateTimeBetween('now','+2 months')->format('Y-m-d H:i'),
        ];
    }
}
