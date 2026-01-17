<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OfferPopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfferPopupController extends Controller
{
    public function index()
    {
        $popups = OfferPopup::orderBy('created_at', 'desc')->get();
        return view('admin.popups.index', compact('popups'));
    }

    public function create()
    {
        return view('admin.popups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'button_text' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'display_delay' => 'integer|min:0',
            'cookie_duration' => 'integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('popups', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        // Enforcement: Only one active popup at a time
        if ($validated['is_active']) {
            OfferPopup::where('is_active', '=', true)->update(['is_active' => false]);
        }

        OfferPopup::create($validated);

        return redirect()->route('popups.admin.index')->with('success', 'Offer popup created successfully.');
    }

    public function edit(OfferPopup $popup)
    {
        return view('admin.popups.edit', compact('popup'));
    }

    public function update(Request $request, OfferPopup $popup)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'button_text' => 'nullable|string|max:255',
            'link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'display_delay' => 'integer|min:0',
            'cookie_duration' => 'integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->hasFile('image')) {
            if ($popup->image_path) {
                Storage::disk('public')->delete($popup->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('popups', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        // Enforcement: Only one active popup at a time
        if ($validated['is_active']) {
            OfferPopup::where('id', '!=', $popup->id)->where('is_active', '=', true)->update(['is_active' => false]);
        }

        $popup->update($validated);

        return redirect()->route('popups.admin.index')->with('success', 'Offer popup updated successfully.');
    }

    public function destroy(OfferPopup $popup)
    {
        if ($popup->image_path) {
            Storage::disk('public')->delete($popup->image_path);
        }
        $popup->delete();
        return redirect()->route('popups.admin.index')->with('success', 'Offer popup deleted successfully.');
    }

    public function preview(OfferPopup $popup)
    {
        return view('layouts.frontend', ['previewPopup' => $popup]);
    }
}
