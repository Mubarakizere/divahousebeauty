<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\{
    CategoryController,
    BrandController,
    GoogleAuthController,
    HomeController,
    ProductController,
    CartController,
    PromotionController,
    OrderController,
    DashboardController,
    BookingController,
    ServiceProvider,
    BlogController,
    ContactController,
    PaymentController,
    AddressController,
    NewsletterController,
    PageController
};

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;

// =========================
// 🔐 Authentication Routes
// =========================
Auth::routes();

// =========================
// 🌐 Public Routes
// =========================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

Route::get('/about', [PageController::class, 'about'])->name('about');

// Products
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Search / Category listing
Route::get('/search', [CategoryController::class, 'show'])->name('search');

// Canonical slug routes
Route::get('/category', [CategoryController::class, 'show'])
    ->name('category'); // index/search (no param)

Route::get('/category/{category:slug}', [CategoryController::class, 'show'])
    ->name('category.show'); // single category

Route::get('/brand/{brand:slug}', [BrandController::class, 'show'])
    ->name('brand.show');

// Legacy numeric ID redirects (keep old links alive)
Route::get('/category/{id}', function ($id) {
    $c = \App\Models\Category::findOrFail($id);
    return redirect()->route('category.show', ['category' => $c->slug], 301);
})->whereNumber('id');

Route::get('/brand/{id}', function ($id) {
    $b = \App\Models\Brand::findOrFail($id);
    return redirect()->route('brand.show', ['brand' => $b->slug], 301);
})->whereNumber('id');

// Blog & Promotions
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::get('/deals', [PromotionController::class, 'index'])->name('deals');

// Contact
Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')   // up to 5 messages/minute
    ->name('contact.store');

// Google OAuth
Route::get('auth/google', [GoogleAuthController::class, 'redirect'])->name('google-auth');
Route::get('auth/google/call-back', [GoogleAuthController::class, 'callbackGoogle']);

// Legacy /home → /
Route::redirect('/home', '/');

// Test page: only top bar + auth modal
Route::view('/home2', 'home2')->name('home2');

// =========================
// 📅 Booking (Public)
// =========================
Route::get('/booking/create', [BookingController::class, 'showBookingForm'])->name('booking.create');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/services/{serviceTypeId}', [BookingController::class, 'getServices']);
Route::get('/providers/{serviceId}', [BookingController::class, 'getProviders']);

// =========================
// 🧪 DEBUG / DIAGNOSTIC ROUTES
// (keep or remove as needed)
// =========================
Route::any('/payment/webhook-test', function(Request $request) {
    Log::info('🧪 Webhook test endpoint hit', [
        'method'  => $request->method(),
        'headers' => $request->headers->all(),
        'body'    => $request->all(),
    ]);

    return response()->json([
        'status'    => 'webhook_accessible',
        'timestamp' => now(),
        'method'    => $request->method(),
    ]);
});

Route::get('/webhook-config-check', function() {
    return response()->json([
        'weflexfy_config' => [
            'access_key'      => config('services.weflexfy.access_key') ? 'SET' : 'MISSING',
            'secret_key'      => config('services.weflexfy.secret_key') ? 'SET' : 'MISSING',
            'business_number' => config('services.weflexfy.business_number'),
        ],
        'webhook_url' => url('/payment/webhook'),
        'app_url'     => config('app.url'),
    ]);
});

Route::get('/test-order-lookup/{token}', function($token) {
    $order = \App\Models\Order::where('payment_token', $token)->first();

    if ($order) {
        return response()->json([
            'found'   => true,
            'order'   => $order,
            'status'  => $order->status,
            'is_paid' => $order->is_paid,
        ]);
    }

    return response()->json([
        'found'         => false,
        'token_searched'=> $token,
        'all_orders'    => \App\Models\Order::select('id', 'payment_token', 'status')
                                ->latest()->take(5)->get(),
    ]);
});

// =========================
// 💳 Payment & Webhook Routes
// =========================
Route::group(['prefix' => 'payment'], function () {
    // Payment initiation
    Route::post('/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');

    // Payment iframe display
    Route::get('/iframe', [PaymentController::class, 'showIframe'])->name('payment.iframe');

    // Payment status polling (AJAX)
    Route::get('/status/{id}', [PaymentController::class, 'checkOrderStatus'])->name('payment.status');

    // Payment success/failure pages
    Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/failed', [PaymentController::class, 'failed'])->name('payment.failed');

    // Payment retry
    Route::get('/retry/{order}', [PaymentController::class, 'retryPayment'])->name('payment.retry');
});

// IMPORTANT: Webhook endpoint (must be accessible without CSRF protection)
Route::post('/payment/webhook', [PaymentController::class, 'handleWebhook'])->name('payment.webhook');

// =========================
// 🔐 Authenticated User Routes
// =========================
Route::middleware(['auth'])->group(function () {

    // Shared dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard API Routes (for AJAX calls)
    Route::prefix('dashboard/api')->group(function () {
        Route::get('/monthly-revenue', [DashboardController::class, 'getMonthlyRevenue'])->name('dashboard.api.monthly-revenue');
        Route::get('/order-status', [DashboardController::class, 'getOrderStatusDistribution'])->name('dashboard.api.order-status');
        Route::get('/top-products/{limit?}', [DashboardController::class, 'getTopProducts'])->name('dashboard.api.top-products');
        Route::get('/recent-activity', [DashboardController::class, 'getRecentActivity'])->name('dashboard.api.recent-activity');
    });

    // Orders & Payment (customer / checkout side)
    Route::post('/place-order', [OrderController::class, 'placeOrder'])->name('order.place');
    Route::get('/order-payment/{order}', [OrderController::class, 'paymentPage'])->name('order.payment');

    // Cart
    Route::post('/addcart/{id}', [HomeController::class, 'addcart']);
    Route::get('/cart', [CartController::class, 'cart'])->name('cart');
    Route::get('/cart-items', [CartController::class, 'getCartItems']);
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Bookings (customer)
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('booking.index');
    Route::get('/dashboard/my-bookings', [DashboardController::class, 'myBookings'])->name('booking.dashboard');

    // Address management routes
    Route::resource('my-addresses', AddressController::class)->names([
        'index'   => 'address.index',
        'create'  => 'address.create',
        'store'   => 'address.store',
        'edit'    => 'address.edit',
        'update'  => 'address.update',
        'destroy' => 'address.destroy',
    ])->except('show')->parameters([
        'my-addresses' => 'address',
    ]);

    // Additional address routes
    Route::patch('my-addresses/{address}/set-default', [AddressController::class, 'setAsDefault'])
        ->name('address.set-default');
});

// =========================
// 🛠 Admin Routes (Spatie Role Protected)
// =========================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->group(function () {

        // Admin main dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->name('admin.dashboard');

        // Admin Products (slug-based, names admin.products.*)
        Route::resource('products', AdminProductController::class)
            ->scoped(['product' => 'slug'])
            ->names('admin.products');

        // Extra product image delete
        Route::delete('/products/{product}/images/{index}', [AdminProductController::class, 'deleteImage'])
            ->name('admin.products.deleteImage');

        // Admin Brands (slug-based, no show)
        Route::resource('brands', AdminBrandController::class)
            ->scoped(['brand' => 'slug'])
            ->except(['show'])
            ->names('admin.brands');

        // Admin Categories CRUD
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)
            ->names('admin.categories');

        // Admin Orders Routes (names admin.orders.*)
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::put('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
        Route::put('/orders/{order}/mark-paid', [AdminOrderController::class, 'markPaid'])->name('admin.orders.markPaid');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');

        // Admin Dashboard API Routes (for admin panel charts)
        Route::prefix('api')->group(function () {
            Route::get('/monthly-revenue', [DashboardController::class, 'getMonthlyRevenue'])->name('admin.api.monthly-revenue');
            Route::get('/order-status-distribution', [DashboardController::class, 'getOrderStatusDistribution'])->name('admin.api.order-status');
            Route::get('/top-products/{limit?}', [DashboardController::class, 'getTopProducts'])->name('admin.api.top-products');

            Route::get('/customers-overview', function () {
                if (!auth()->user()->hasRole('admin')) {
                    abort(403);
                }

                $totalCustomers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'customer');
                })->count();

                $newCustomersToday = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'customer');
                })->whereDate('created_at', today())->count();

                $activeCustomers = \App\Models\User::whereHas('roles', function ($query) {
                    $query->where('name', 'customer');
                })->whereHas('orders')->count();

                return response()->json([
                    'total_customers'  => $totalCustomers,
                    'new_today'        => $newCustomersToday,
                    'active_customers' => $activeCustomers,
                ]);
            })->name('admin.api.customers-overview');
        });
    });

// =========================
// 🧪 Testing Routes
// =========================
Route::get('/admin/test-auth', function () {
    return '✅ You are allowed';
})->middleware(['auth', 'role:admin']);
