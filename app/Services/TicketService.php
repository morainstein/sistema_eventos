<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Promoter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TicketService
{
    public function calculateTicketsFinalPrice(Request $request)
    {
        $batch = $request->batch;
        $coupon = $request->coupon;
        $finalPrice = $batch->price;

        if(!$coupon){
            return $batch->price;
        }

        $discount = Discount::whereIsValid()
            ->findByCoupon($coupon)
            ->whereEventId($batch->event_id)
            ->first();
        
        if(!$discount){
            throw new ModelNotFoundException();
        }

        $finalPrice = (new DiscountService($discount,$batch))->calculateDiscount();

        $discount->increment('times_used');
        $discount->save();

        return $finalPrice;
    }
}