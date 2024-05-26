<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    function handleProviderCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'google_id' => $googleUser->id,
        ], [
            'full_name' => $googleUser->name,
            'email' => $googleUser->email,
            'password' => Str::password(12),
        ]);

        Auth::login($user);

        $response = [
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user
        ];

        $cookie = cookie('jwt', $response['token'], 60*24);

        return redirect(env('FRONTEND_URL'))->withCookie($cookie);
    }

    function handleProviderRedirect()
    {
        return Socialite::driver('google')->redirect();
    }
}
