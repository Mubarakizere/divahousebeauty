<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite; 
use Illuminate\Support\Facades\Auth; 
use App\Models\User;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect(); 
    }

    /**
     * Handle Google callback.
     */
    public function callbackGoogle()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Update missing google_id if needed
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            } else {
                // Create new user with Google info
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(str()->random(16)),
                ]);

                // Default role = customer
                $user->assignRole('customer');
            }

            Auth::login($user);

            // Redirect based on role
            if ($user->hasRole('admin')) {
                return redirect('/dashboard')->with('success', 'Welcome back, Admin ' . $user->name);
            }

            return redirect('/dashboard')->with('success', 'Welcome, ' . $user->name . '!');

        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Google login failed: ' . $th->getMessage());
        }
    }
}
