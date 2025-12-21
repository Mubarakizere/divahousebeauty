<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Cart;

class PageController extends Controller
{
    public function about()
    {
        $categories = Category::select('id','name','slug')->get();
        $count = auth()->check()
            ? Cart::where('users_id', auth()->id())->count()
            : 0;

        return view('about', compact('categories','count'));
    }
}
