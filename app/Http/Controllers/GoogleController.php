<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller 
{
    // Redirect the user to Google's OAuth page
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    // Handle callback from Google OAuth
    public function handleGoogleCallback() {
        $googleUser = Socialite::driver('google')->user(); // Retrieve user from Google
        
        // Find the user by Google ID
        $findUser = User::where('google_id', $googleUser->id)->first();
        
        if ($findUser) {
            // If user exists, log them in
            Auth::login($findUser);
        } else {
            // Create a new user if not found
            $newUser = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name' => $googleUser->name,
                    'google_id' => $googleUser->id,
                    'password' => encrypt('12345678'), // Placeholder password
                ]
            );
            // Log in the newly created user
            Auth::login($newUser);
        }
        
        // Redirect to intended page (default is 'home')
        return redirect()->intended('home');
    }
}