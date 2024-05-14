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
        // Validate the request

        $user = User::create([
            'full_name' => $request->input('fullName'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        return $user;
    }

    function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60*24);

        return response([
            'message' => $token
        ])->withCookie($cookie);
    }

    function getuser(Request $request)
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
