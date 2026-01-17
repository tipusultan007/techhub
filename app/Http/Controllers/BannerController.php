<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::all()->keyBy('position');
        
        // Ensure all 3 positions exist in the database for the view
        $positions = ['main', 'side_top', 'side_bottom'];
        foreach ($positions as $pos) {
            if (!$banners->has($pos)) {
                $banners->put($pos, new Banner(['position' => $pos, 'title' => '']));
            }
        }

        return view('admin.banners.index', compact('banners'));
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
        ]);

        $banner->update($request->only(['title', 'subtitle', 'badge_text', 'button_text', 'link']));

        if ($request->hasFile('banner_image')) {
            $banner->addMediaFromRequest('banner_image')->toMediaCollection('banner_image');
        }

        return back()->with('success', ucfirst(str_replace('_', ' ', $banner->position)) . ' banner updated successfully.');
    }
}
