<?php

namespace App\Traits;

use Illuminate\Support\Collection;

trait TracksRecentlyViewed
{
    /**
     * Track this product as recently viewed
     */
    public function trackView(): void
    {
        $recent = session()->get('recently_viewed', []);
        
        // Remove current product if it exists
        $recent = array_filter($recent, fn($id) => $id != $this->id);
        
        // Add to beginning of array
        array_unshift($recent, $this->id);
        
        // Keep only last 10 items
        $recent = array_slice($recent, 0, 10);
        
        session()->put('recently_viewed', $recent);
    }
    
    /**
     * Get recently viewed products
     */
    public static function getRecentlyViewed(int $limit = 6, ?int $excludeId = null): Collection
    {
        $ids = session()->get('recently_viewed', []);
        
        if ($excludeId) {
            $ids = array_filter($ids, fn($id) => $id != $excludeId);
        }
        
        $ids = array_slice($ids, 0, $limit);
        
        if (empty($ids)) {
            return collect();
        }
        
        // Maintain the order from session
        return static::whereIn('id', $ids)
            ->get()
            ->sortBy(function($product) use ($ids) {
                return array_search($product->id, $ids);
            })
            ->values();
    }
}
