<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected ?Admin $user; 

    public function __construct()
    {
        $this->user = request()->user('adminGuard') ?? null;
    }

    public function store(CreateUserRequest $request)
    {
        $admin = new Admin([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

        $admin->registry = $request->registry;


        $admin->save();
        return response()->json(['message' => 'Admin registered successfully'], 201);
    }

    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('registry', 'password');
        
        $admin = Admin::where('registry', $request->registry)->first();

        $login = Hash::check($credentials['password'], $admin->password ?? false);

        if (!$login) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $admin->createToken('AdminToken', [UserRoleEnum::ADMIN]);
        return response()->json(['token' => $token->plainTextToken], 200);
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
