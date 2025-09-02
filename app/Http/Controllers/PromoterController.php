<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Promoter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

        $promoter->registry = $request->registry;

        $promoter->save();
        return response()->json(['message' => 'Promoter registered successfully'], 201);
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

    public function show(Promoter $promoter)
    {
        return $promoter;
    }

    public function update(Request $request)
    {
        Auth::user()->fill($request->all())->save();

        return response()->json(['message' => 'Promoter updated successfully']);
    }

    public function destroy()
    /**
     * Soft delete
     */
    {
        Auth::user()->delete();

        return response()->json(['message' => 'Promoter has been soft deleted'], 200);
    }
}
