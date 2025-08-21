<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

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
            'registry' => $request->registry,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

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

    /**
     * Display the specified resource.
     */
    public function show(Request $request)       
    {

        dd($this->user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
