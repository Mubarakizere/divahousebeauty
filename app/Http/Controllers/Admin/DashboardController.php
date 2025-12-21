<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $ordersCount = Order::count();
        $productsCount = Product::count();
        $pendingOrders = Order::where('status', 'pending_payment')->count();

        return view('admin.dashboard', compact('usersCount', 'ordersCount', 'productsCount', 'pendingOrders'));
    }
}
