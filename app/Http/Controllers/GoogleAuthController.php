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
        // Store the intended URL in the session if it's set
        if (request()->has('previous_url')) {
            session(['url.intended' => request('previous_url')]);
        }
        
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
                // Update missing google_id or avatar if needed
                $user->google_id = $googleUser->getId();
                // Update avatar if not present or changed (optional logic, here we just update if empty)
                if (!$user->avatar) {
                    $user->avatar = $googleUser->getAvatar();
                }
                $user->save();
            } else {
                // Create new user with Google info
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => bcrypt(str()->random(16)),
                ]);

                // Default role = customer
                $user->assignRole('customer');
            }

            Auth::login($user);

            // Redirect based on role or intended
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back, Admin ' . $user->name);
            }

            // Prevent redirecting to static .html pages if they were stored in session
            $intended = session('url.intended');
            if ($intended && str_contains($intended, '.html')) {
                session()->forget('url.intended');
            }

            return redirect()->intended(route('dashboard'))->with('success', 'Welcome, ' . $user->name . '!');

        } catch (\Throwable $th) {
            return redirect()->route('login')->with('error', 'Google login failed: ' . $th->getMessage());
        }
    }
}
