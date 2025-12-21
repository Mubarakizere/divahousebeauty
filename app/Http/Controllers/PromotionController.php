<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    public function index()
{
    $promotions = Promotion::with('product')->get();

    dd($promotions->toArray()); // Check if 'product' is now included

    return view('deals', compact('promotions'));
}
}
