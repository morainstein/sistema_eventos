<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Promoter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PromoterController extends Controller
{
    protected ?Promoter $user; 

    public function __construct()
    {
        $this->user = request()->user('promoterGuard') ?? null;
    }

    public function store(CreateUserRequest $request)
    {
        $promoter = new Promoter([
            'name' => $request->name,
            'registry' => $request->registry,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

        $promoter->save();
        return response()->json(['message' => 'Admin registered successfully'], 201);
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('registry', 'password');
        
        $promoter = Promoter::where('registry', $request->registry)->first();

        $login = Hash::check($credentials['password'], $promoter->password ?? false);

        if (!$login) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $promoter->createToken('PromoterToken', [UserRoleEnum::PROMOTER]);
        return response()->json(['token' => $token->plainTextToken], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Promoter $promoter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promoter $promoter)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promoter $promoter)
    {
        //
    }
}
