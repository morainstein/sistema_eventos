<?php

namespace App\Services;

use App\Enums\DiscountType;
use App\Mail\NotifyCustomerTicketPurchaseSuccessfullyMail;
use App\Mail\NotifyPromoterTicketPurchaseMail;
use App\Models\Discount;
use App\Models\Promoter;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketService
{
    static public function calculateTicketsFinalPrice(Request $request)
    {
        $batch = $request->batch;
        $coupon = $request->coupon;
        $finalPrice = $batch->price;

        if(!$coupon){
            return $finalPrice;
        }

        $discount = Discount::where('coupon_code', $coupon)
            ->where('valid_until', '>', now())
            ->where('event_id', $batch->event_id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if(!$discount){
            throw new ModelNotFoundException();
        }
        
        $discountIsValid = $discount->times_used <= $discount->usage_limit ?
            true : false;

        if ($discountIsValid) {
            if ($discount->discount_type === DiscountType::FIXED->value) {
                $finalPrice -= $discount->discount_amount;
            } elseif ($discount->discount_type === DiscountType::PERCENTAGE->value) {
                $finalPrice -= ($finalPrice * $discount->discount_amount / 100);
            }

            $discount->increment('times_used');
        }

        return $finalPrice;
    }
}