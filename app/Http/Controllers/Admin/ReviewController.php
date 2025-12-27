<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display all reviews with filtering
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Review::with(['user:id,name', 'product:id,name,slug'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reviews = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => Review::count(),
            'pending' => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'rejected' => Review::rejected()->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats', 'status'));
    }

    /**
     * Approve a review
     */
    public function approve($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'approved']);

        return back()->with('message', 'Review approved successfully!');
    }

    /**
     * Reject a review
     */
    public function reject($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'rejected']);

        return back()->with('message', 'Review rejected successfully!');
    }

    /**
     * Delete a review permanently
     */
    public function destroy($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->delete();

        return back()->with('message', 'Review deleted successfully!');
    }
}
