<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $mainBanners = Banner::where('position', '=', 'main')->orderBy('order', 'asc')->get();
        $sideTop = Banner::where('position', '=', 'side_top')->first() ?? new Banner(['position' => 'side_top']);
        $sideBottom = Banner::where('position', '=', 'side_bottom')->first() ?? new Banner(['position' => 'side_bottom']);

        return view('admin.banners.index', compact('mainBanners', 'sideTop', 'sideBottom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'banner_image' => 'required|image|max:2048',
            'position' => 'required|in:main,side_top,side_bottom',
            'order' => 'nullable|integer',
        ]);

        $banner = Banner::create($request->only(['title', 'subtitle', 'badge_text', 'button_text', 'link', 'position', 'order']));

        if ($request->hasFile('banner_image')) {
            $banner->addMediaFromRequest('banner_image')->toMediaCollection('banner_image');
        }

        return back()->with('success', 'Banner created successfully.');
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'banner_image' => 'nullable|image|max:2048',
            'order' => 'nullable|integer',
        ]);

        $banner->update($request->only(['title', 'subtitle', 'badge_text', 'button_text', 'link', 'order']));

        if ($request->hasFile('banner_image')) {
            $banner->addMediaFromRequest('banner_image')->toMediaCollection('banner_image');
        }

        return back()->with('success', ucfirst(str_replace('_', ' ', $banner->position)) . ' banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return back()->with('success', 'Banner deleted successfully.');
    }
}
