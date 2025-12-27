<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect admins to admin dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        
        $isAdmin = $user->role === 'admin';
        $isCustomer = $user->role === 'customer';

        // Admin logic
        if ($isAdmin) {
            // Total stats
            $totalOrders = Order::count();
            $totalRevenue = Order::sum('total');
            $totalCustomers = User::where('role', 'customer')->count();
            $totalProducts = Product::count();
            
            // Today's stats
            $todayOrders = Order::whereDate('created_at', today())->count();
            $todayRevenue = Order::whereDate('created_at', today())->sum('total');
            $todayCustomers = User::where('role', 'customer')
                ->whereDate('created_at', today())->count();
            $pendingOrders = Order::where('status', 'pending_payment')->count();
            
            // Recent orders for admin view
            $recentOrders = Order::with(['user', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return view('dashboard', [
                'user' => $user,
                'isAdmin' => true,
                'totalOrders' => $totalOrders,
                'totalRevenue' => $totalRevenue,
                'totalCustomers' => $totalCustomers,
                'totalProducts' => $totalProducts,
                'todayOrders' => $todayOrders,
                'todayRevenue' => $todayRevenue,
                'todayCustomers' => $todayCustomers,
                'pendingOrders' => $pendingOrders,
                'recentOrders' => $recentOrders,
                'orders' => collect(), // Empty collection for blade compatibility
            ]);
        }

        // Customer logic
        if ($isCustomer) {
            // Customer orders
            $orders = Order::with(['items.product'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return view('dashboard_client', [
                'user' => $user,
                'orders' => $orders,
                'isCustomer' => true,
                'isAdmin' => false,
                // Add empty admin variables for blade compatibility if strictly needed, 
                // but dashboard_client shouldn't need them.
            ]);
        }

        // Fallback (guest or unassigned roles)
        return redirect('/')->with('error', 'Unauthorized access.');
    }

    // Additional helper methods for dashboard widgets

    /**
     * Get monthly revenue chart data (for admin dashboard)
     */
    public function getMonthlyRevenue()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $monthlyData = Order::selectRaw('MONTH(created_at) as month, SUM(total) as revenue')
            ->whereYear('created_at', date('Y'))
            ->where('status', '!=', 'cancelled')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($monthlyData);
    }

    /**
     * Get order status distribution (for admin dashboard)
     */
    public function getOrderStatusDistribution()
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $statusData = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return response()->json($statusData);
    }

    /**
     * Get top selling products (for admin dashboard)
     */
    public function getTopProducts($limit = 5)
    {
        if (!auth()->user() || auth()->user()->role !== 'admin') {
            abort(403);
        }

        $topProducts = Product::withCount(['orderItems as total_sold' => function($query) {
            $query->selectRaw('SUM(quantity)');
        }])
        ->orderBy('total_sold', 'desc')
        ->take($limit)
        ->get();

        return response()->json($topProducts);
    }

    /**
     * Get recent activity for customer dashboard
     */
    public function getRecentActivity()
    {
        $user = auth()->user();
        
        if ($user->role !== 'customer') {
            abort(403);
        }

        $recentOrders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'orders' => $recentOrders
        ]);
    }
}