<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Event;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    public function index(string $eventId)
    {
        $discounts = Event::with('discounts')->find($eventId)->discounts;

        return response()->json($discounts,200);
    }

    public function store(Request $request)
    {
        $discount = new Discount($request->all());
        $discount->save();
        
        return response()->json(['message' => 'Discount registered successfully'],201);
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();
        
        return response()->json(['message' => 'Discount destroyed successfully'], 200);
    }
}
