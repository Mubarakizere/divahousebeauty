<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total stats
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total');
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();

        // Today's stats
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total');
        $newCustomersToday = User::where('role', 'customer')->whereDate('created_at', today())->count();
        
        // Order statuses
        $pendingOrders = Order::whereIn('status', ['pending_payment', 'processing'])->count();
        $completedOrders = Order::where('status', 'completed')->count();

        // Low stock alerts (items below 5 units)
        $lowStockProducts = Product::where('quantity', '<', 5)->get();
        $lowStockCount = $lowStockProducts->count();

        // Recent orders
        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
            
        // Top selling products for initial load if needed
        $topProducts = Product::withCount(['orderItems as total_sold' => function($query) {
            $query->selectRaw('SUM(quantity)');
        }])
        ->orderBy('total_sold', 'desc')
        ->take(5)
        ->get();

        return view('admin.dashboard', compact(
            'totalOrders', 'totalRevenue', 'totalCustomers', 'totalProducts',
            'todayOrders', 'todayRevenue', 'newCustomersToday',
            'pendingOrders', 'completedOrders', 'recentOrders', 
            'lowStockCount', 'lowStockProducts', 'topProducts'
        ));
    }

    /**
     * Get monthly revenue chart data
     */
    public function getMonthlyRevenue()
    {
        $monthlyData = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', date('Y'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyData);
    }

    /**
     * Get order status distribution
     */
    public function getOrderStatusDistribution()
    {
        $statusData = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json($statusData);
    }

    /**
     * Get top selling products
     */
    public function getTopProducts($limit = 5)
    {
        $topProducts = Product::withCount(['orderItems as total_sold' => function($query) {
            $query->selectRaw('SUM(quantity)');
        }])
        ->orderBy('total_sold', 'desc')
        ->take($limit)
        ->get();

        return response()->json($topProducts);
    }
}
