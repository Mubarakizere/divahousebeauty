<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Get all approved reviews for a product (AJAX)
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        
        $reviews = $product->approvedReviews()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'averageRating' => $product->average_rating,
            'totalReviews' => $product->review_count,
        ]);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, $productId)
    {
        $user = Auth::user();
        $product = Product::findOrFail($productId);

        // Check if user has purchased this product
        $hasPurchased = $user->hasPurchased($product->id);

        // Validate request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'review' => 'nullable|string|max:1000',
        ]);

        // Check for existing review
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product. You can edit your existing review.',
            ], 422);
        }

        // Create review
        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'review' => $validated['review'] ?? null,
            'verified_purchase' => $hasPurchased,
            'status' => 'approved', // Auto-approve for now, can be changed to 'pending' if moderation needed
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review!',
            'review' => $review->load('user:id,name'),
        ]);
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, $reviewId)
    {
        $user = Auth::user();
        $review = Review::findOrFail($reviewId);

        // Ensure user owns this review
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:100',
            'review' => 'nullable|string|max:1000',
        ]);

        // Update review
        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'review' => $validated['review'] ?? null,
            'status' => 'approved', // Reset to pending if you want re-moderation after edits
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully!',
            'review' => $review->load('user:id,name'),
        ]);
    }

    /**
     * Check if user can review a product
     */
    public function canReview($productId)
    {
        if (!Auth::check()) {
            return response()->json([
                'canReview' => false,
                'reason' => 'not_authenticated',
                'message' => 'Please log in to leave a review',
            ]);
        }

        $user = Auth::user();
        $product = Product::findOrFail($productId);

        // Check if already reviewed
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'canReview' => false,
                'reason' => 'already_reviewed',
                'message' => 'You have already reviewed this product',
                'reviewId' => $existingReview->id,
                'review' => $existingReview,
            ]);
        }

        // Check if purchased
        $hasPurchased = $user->hasPurchased($product->id);

        return response()->json([
            'canReview' => true,
            'hasPurchased' => $hasPurchased,
        ]);
    }
}
