<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller

{
    public function create(Request $request)
    {
        try{
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        DB::beginTransaction();
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            // $token = $user->createToken('auth_token')->plainTextToken;
            
            event(new Registered($user));
            $response = [
                'user' => $user,
                // 'token' => $token
            ];

            return response($response, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    public function login()
    {
        redirect('/');
    }
}
