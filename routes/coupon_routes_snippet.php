<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController, ProductController, CategoryController, 
    CartController, OrderController, ReviewController,
    CouponController, WishlistController
};
use App\Http\Controllers\Admin\{
    AdminProductController, AdminCategoryController, AdminBrandController,
    CouponController as AdminCouponController, DashboardController as AdminDashboardController
};

// Import remaining facades/controllers as needed
Route::post('/coupon/apply', [CouponController::class, 'apply'])->middleware('auth')->name('coupon.apply');
Route::delete('/coupon/remove', [CouponController::class, 'remove'])->middleware('auth')->name('coupon.remove');

// Admin coupons routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('coupons', AdminCouponController::class)->names('admin.coupons');
    Route::post('coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('admin.coupons.toggle');
});
