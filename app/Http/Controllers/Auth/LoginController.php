<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Category;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Show the login form and pass categories to the view.
     */
    public function showLoginForm()
    {
        $categories = Category::all(); // optional for the login view
        return view('auth.login', compact('categories'));
    }

    /**
     * Redirect users after login using Spatie roles.
     */
    protected function authenticated(Request $request, $user)
    {
        // Send everyone to the shared /dashboard route
        // Redirect to intended page or dashboard
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
