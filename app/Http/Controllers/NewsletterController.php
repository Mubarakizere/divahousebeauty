<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsletterSubscriber;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // honeypot "website" must be empty
        $request->validate([
            'email'   => 'required|email:rfc,dns|max:190',
            'website' => 'nullable|size:0',
        ]);

        NewsletterSubscriber::firstOrCreate([
            'email' => strtolower($request->email),
        ]);

        return response()->json(['ok' => true]);
    }
}
