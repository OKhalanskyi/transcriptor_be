<?php

use App\Http\Controllers\SocialiteController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/google/redirect', [SocialiteController::class, 'handleProviderRedirect']);
Route::get('/auth/google/callback', [SocialiteController::class, 'handleProviderCallback']);

