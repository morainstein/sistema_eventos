<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\Discount;

class DiscountService
{
    public function __construct(
        public readonly Discount $discount,
        public readonly ?Batch $batch
    )
    {
    }

    /**
     * @return int $finalPrice
     */
    public function calculateDiscount(): int
    {
        $discount = $this->discount;
        $batch = $this->batch;
        $finalPrice = $batch->price;

        if(!$this->isDiscountValid()){
            return $finalPrice;
        }

        if ($discount->discount_type === Discount::FIXED){
            $finalPrice = self::applyFixedDiscount($batch->price, $discount->discount_amount);

        } elseif ($discount->discount_type === Discount::PERCENTAGE){
            $finalPrice = self::applyPercentageDiscount($finalPrice, $discount->discount_amount);

        }

        return $finalPrice;
    }

    public function isDiscountValid(): bool
    {
        return $this->discount->times_used < $this->discount->usage_limit;
    }

    static public function applyFixedDiscount(int $price, int $discountAmount): int
    {
        return $price - $discountAmount;
    }

    static public function applyPercentageDiscount(int $price, int $discountAmount): int
    {
        return (int) $price - round($price * $discountAmount / 100);
    }
}
