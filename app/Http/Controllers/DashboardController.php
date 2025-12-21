<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isCustomer = $user->hasRole('customer');

        // Admin logic
        if ($isAdmin) {
            // Total stats
            $totalOrders = Order::count();
            $totalRevenue = Order::sum('total');
            $totalCustomers = User::whereHas('roles', function($query) {
                $query->where('name', 'customer');
            })->count();
            $totalProducts = Product::count();
            
            // Today's stats
            $todayOrders = Order::whereDate('created_at', today())->count();
            $todayRevenue = Order::whereDate('created_at', today())->sum('total');
            $todayCustomers = User::whereHas('roles', function($query) {
                $query->where('name', 'customer');
            })->whereDate('created_at', today())->count();
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
                'bookings' => collect(), // Empty collection for blade compatibility
            ]);
        }

        // Customer logic
        if ($isCustomer) {
            // Customer orders
            $orders = Order::with(['items.product'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Customer bookings - using customer_id as shown in your database structure
            $bookings = collect(); // Default empty collection
            
            try {
                // Get bookings using customer_id (based on your database structure)
                $bookings = Booking::with(['services', 'provider'])
                    ->where('customer_id', $user->id) // Using user->id as customer_id
                    ->orderBy('created_at', 'desc')
                    ->get();
            } catch (\Exception $e) {
                // If there's still an error, keep empty collection
                \Log::warning('Bookings query failed: ' . $e->getMessage());
                $bookings = collect();
            }

            return view('dashboard', [
                'user' => $user,
                'orders' => $orders,
                'bookings' => $bookings,
                'isCustomer' => true,
                'isAdmin' => false,
                // Add empty admin variables for blade compatibility
                'totalOrders' => 0,
                'totalRevenue' => 0,
                'totalCustomers' => 0,
                'totalProducts' => 0,
                'todayOrders' => 0,
                'todayRevenue' => 0,
                'todayCustomers' => 0,
                'pendingOrders' => 0,
                'recentOrders' => collect(),
            ]);
        }

        // Fallback (guest or unassigned roles)
        return redirect('/')->with('error', 'Unauthorized access.');
    }

    // For customers only
    public function myBookings()
    {
        $user = auth()->user();
        
        try {
            // Based on your database structure, use customer_id with user->id
            $bookings = Booking::with(['services', 'provider'])
                ->where('customer_id', $user->id)
                ->latest()
                ->paginate(10);
        } catch (\Exception $e) {
            \Log::error('Bookings query failed: ' . $e->getMessage());
            $bookings = collect()->paginate(10); // Empty paginated collection
        }

        return view('dashboard.my-bookings', compact('bookings'));
    }

    // Additional helper methods for dashboard widgets

    /**
     * Get monthly revenue chart data (for admin dashboard)
     */
    public function getMonthlyRevenue()
    {
        if (!auth()->user()->hasRole('admin')) {
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
        if (!auth()->user()->hasRole('admin')) {
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
        if (!auth()->user()->hasRole('admin')) {
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
        
        if (!$user->hasRole('customer')) {
            abort(403);
        }

        $recentOrders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentBookings = collect();
        try {
            // Use customer_id with user->id based on your database structure
            $recentBookings = Booking::with(['services'])
                ->where('customer_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            \Log::warning('Recent bookings query failed: ' . $e->getMessage());
        }

        return response()->json([
            'orders' => $recentOrders,
            'bookings' => $recentBookings
        ]);
    }
}