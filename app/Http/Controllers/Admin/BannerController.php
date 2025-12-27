<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of banners
     */
    public function index()
    {
        $banners = Banner::orderBy('position')->ordered()->get();
        
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        $positions = [
            'hero_main' => 'Hero Main Slider',
            'hero_side' => 'Hero Side Banner',
            'category_top' => 'Category Top',
            'mid_page' => 'Mid Page',
            'footer_above' => 'Above Footer',
        ];
        
        return view('admin.banners.create', compact('positions'));
    }

    /**
     * Store a newly created banner
     */
    public function store(Request $request)
    {
        \Log::info('Banner Store Request Started', [
            'all_data' => $request->all(),
            'has_file' => $request->hasFile('image'),
            'file_details' => $request->hasFile('image') ? [
                'original_name' => $request->file('image')->getClientOriginalName(),
                'size' => $request->file('image')->getSize(),
                'mime' => $request->file('image')->getMimeType(),
            ] : null
        ]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'position' => 'required|in:hero_main,hero_side,category_top,mid_page,footer_above',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:30720',
                'link' => 'nullable|url|max:255',
                'link_text' => 'nullable|string|max:50',
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'order' => 'nullable|integer|min:0',
                'target' => 'required|in:_self,_blank',
            ]);

            \Log::info('Validation passed', ['validated' => $validated]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('banners', 'public');
                $validated['image'] = $path;
                \Log::info('Image stored', ['path' => $path]);
            }

            // Set defaults
            $validated['is_active'] = $request->has('is_active');
            $validated['order'] = $validated['order'] ?? 0;

            $banner = Banner::create($validated);
            \Log::info('Banner created successfully', ['banner_id' => $banner->id]);

            return redirect()->route('admin.banners.index')
                ->with('success', 'Banner created successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Banner Store Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create banner: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a banner
     */
    public function edit(Banner $banner)
    {
        $positions = [
            'hero_main' => 'Hero Main Slider',
            'hero_side' => 'Hero Side Banner',
            'category_top' => 'Category Top',
            'mid_page' => 'Mid Page',
            'footer_above' => 'Above Footer',
        ];
        
        return view('admin.banners.edit', compact('banner', 'positions'));
    }

    /**
     * Update the specified banner
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|in:hero_main,hero_side,category_top,mid_page,footer_above',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:30720',
            'link' => 'nullable|url|max:255',
            'link_text' => 'nullable|string|max:50',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'order' => 'nullable|integer|min:0',
            'target' => 'required|in:_self,_blank',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        // Set active status
        $validated['is_active'] = $request->has('is_active');
        $validated['order'] = $validated['order'] ?? $banner->order;

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner updated successfully!');
    }

    /**
     * Remove the specified banner
     */
    public function destroy(Banner $banner)
    {
        // Delete image file
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner deleted successfully!');
    }
}
