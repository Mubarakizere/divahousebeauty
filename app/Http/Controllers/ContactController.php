<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        // for your Tailwind header partial
        $categories = Category::select('id','name','slug')->get();
        $count = auth()->check()
            ? Cart::where('users_id', auth()->id())->count()
            : 0;

        return view('contact', compact('categories','count'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required','string','max:255'],
            'email'   => ['required','email','max:255'],
            'subject' => ['nullable','string','max:255'],
            'message' => ['required','string','max:5000'],
            // Honeypot (hidden field must stay empty)
            'website' => ['nullable','size:0'],
        ], [
            'website.size' => 'Spam detected.',
        ]);

        ContactMessage::create([
            'name'    => $validated['name'],
            'email'   => $validated['email'],
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
            'replied' => false,
        ]);

        return back()->with('message', 'Thank you! Your message has been received. We’ll get back to you soon.');
    }
}
