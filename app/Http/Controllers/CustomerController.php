<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    protected ?Customer $user; 

    public function __construct()
    {
        $this->user = request()->user('customerGuard') ?? null;
    }

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
