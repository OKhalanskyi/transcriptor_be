<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    function register(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'full_name' => $request->input('fullName'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        $response = [
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user
        ];

        $cookie = cookie('jwt', $response['token'], 60*24);

        return response($response)->withCookie($cookie);
    }

    function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $response = [
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user
        ];

        $cookie = cookie('jwt', $response['token'], 60*24);

        return response($response)->withCookie($cookie);
    }

    function getUser(Request $request)
    {
        $user = $request->user();

        return $user;
    }

    function logout(Request $request)
    {
        $cookie = Cookie::forget('jwt');

        return response([
            'message' => 'Success'
        ])->withCookie($cookie);
    }
}
