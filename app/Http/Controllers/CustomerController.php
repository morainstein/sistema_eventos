<?php

namespace App\Http\Controllers;

use App\Enums\PaggueLinks;
use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Batch;
use App\Models\Customer;
use App\Models\Promoter;
use App\Models\Ticket;
use App\Services\PagguePaymentService;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{

    public function store(CreateUserRequest $request)
    {
        $customer = new Customer([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

        $customer->registry = $request->registry;

        $customer->save();
        return response()->json(['message' => 'Customer registered successfully'], 201);
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

    public function buyTicket(Batch $batch, Request $request)
    {
        DB::beginTransaction();
 
            try{
                $finalPrice = TicketService::calculateTicketsFinalPrice($request);
            }catch(ModelNotFoundException $e){
                DB::rollBack();
                return response()->json(['message' => 'Coupon does not exists'],404);
            }

            try{
                $ticket = new Ticket([
                    'event_id' => $batch->event_id,
                    'batch_id' => $batch->id,
                    'user_id' => Auth::user()->id,
                    'final_price' => $finalPrice
                ]);

                $ticket->save();
            }catch (\PDOException $e) {
                DB::rollBack();
                $message = 'Error purchasing ticket. Try again or contact support.';
                return response()->json(['message' => $message], 500);
            }
            
            try{
                $promoter = Promoter::find($batch->event->promoter_id);
                $pixKey = PagguePaymentService::credentials($promoter->credentials)
                    ->buyTicketByPixStatic($ticket);

            }catch(RequestException $e){
                DB::rollBack();
                $message = 'Error in payment service. Try again or contact support.';
                return response()->json(['message' => $message], 500);
            }
        
        DB::commit();

        return response()->json([
            'amount' => $finalPrice,
            'pix_key' => $pixKey
        ], 201);
    }

    public function show()
    {
        return Auth::user();
    }

    public function update(UpdateUserRequest $request)
    {
        Auth::user()->fill($request->all())->save();

        return response()->json(['message' => 'Customer updated successfully'],200);
    }

    public function destroy()
    /**
     * Soft delete
     */
    {
        Auth::user()->delete();

        return response()->json(['message' => 'Customer has been soft deleted'], 200);
    }
}
