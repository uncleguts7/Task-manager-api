<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=> 'required|string',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8',
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> bcrypt($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'=> $user,
            'token'=> $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required|min:8',
        ]);

        if(!Auth::attempt($request->only('email','password')))
        {
            return response()->json(['message'=>'invalid credentials!.'], 401);
        }else {
            $user = User::where('email', $request->email)->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user'=> $user,
                'token'=> $token,
            ], 200);
        }
    }
}
