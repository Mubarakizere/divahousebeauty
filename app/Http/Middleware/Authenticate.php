<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // Store the last page the user was trying to visit
            Session::put('url.intended', url()->current());

            session()->flash('error', 'Access Denied! You must log in first.');
            return route('home'); // Redirect to home page
        }
    }
}

