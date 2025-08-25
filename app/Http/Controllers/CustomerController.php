<?php

namespace App\Http\Controllers;

use App\Enums\DiscountType;
use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Event;
use App\Models\Ticket;
use App\Services\PagguePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{

    public function store(CreateUserRequest $request)
    {
        $customer = new Customer([
            'name' => $request->name,
            'registry' => $request->registry,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

        $customer->save();
        return response()->json(['message' => 'Admin registered successfully'], 201);
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('registry', 'password');
        
        $customer = Customer::where('registry', $request->registry)->first();

        $login = Hash::check($credentials['password'], $customer->password ?? false);

        if (!$login) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $customer->createToken('CustomerToken', [UserRoleEnum::CUSTOMER]);
        return response()->json(['token' => $token->plainTextToken], 200);
    }

    public function buyTicket(Batch $batch, Request $request, PagguePaymentService $paymentService)
    {
        $coupon = $request->coupon ?? null;

        $final_price = $batch->price;

        DB::beginTransaction();

        // Verifica o cupon e aplica o desconto
            if ($coupon) {
                $discount = Discount::where('coupon_code', $coupon)
                    ->where('valid_until', '>', now())
                    ->orderBy('created_at', 'desc')
                    ->first();

                $discountIsValid = $discount->times_used <= $discount->usage_limit ?
                    true : false;

                if ($discountIsValid) {
                    if ($discount->discount_type === DiscountType::FIXED->value) {
                        $final_price -= $discount->discount_amount;
                    } elseif ($discount->discount_type === DiscountType::PERCENTAGE->value) {
                        $final_price -= ($final_price * $discount->discount_amount / 100);
                    }

                    $discount->increment('times_used');
                }
            }

        // Salva o ingresso            
            try{
                $ticket = new Ticket([
                    'batch_id' => $batch->id,
                    'user_id' => Auth::user()->id,
                    'final_price' => $final_price
                ]);

                $ticket->save();
                $batch->increment('tickets_sold');
            }catch (\PDOException $e) {
                DB::rollBack();
                $message = 'Error purchasing ticket. Try again or contact support.';
                return response()->json(['message' => $message], 500);
            }

        // DB::commit();

        /** 
         * EFETUAR REQUISIÇÃO PARA O PAGAMENTO
         * Create Pix Static {POST -> https://ms.paggue.io/cashin/api/billing_order}
         * */ 
        $body = [
            "external_id" => $ticket->id,
            "amount" => $ticket->final_price,
            "description" => "Pagamento do ingresso #{$ticket->id}",
            "payer_name" => Auth::user()->name,
        ];

        $req = $paymentService->body($body);

        dd($req);
            // ->post('https://ms.paggue.io/cashin/api/billing_order', $body)
            // ->throw();


        return response()->json(['message' => 'Ticket purchased successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
