<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stores = \App\Models\Store::sorted()->get();
        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'whatsapp' => 'nullable|string',
            'map_iframe' => 'nullable|string',
            'hours' => 'nullable|array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);
        
        // Handle boolean unchecked checkboxes
        $data['is_main'] = $request->has('is_main');
        $data['is_active'] = $request->has('is_active');

        \App\Models\Store::create($data);

        return redirect()->route('stores.index')->with('success', 'Store created successfully.');
    }

    public function edit(\App\Models\Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, \App\Models\Store $store)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'whatsapp' => 'nullable|string',
            'map_iframe' => 'nullable|string',
            'hours' => 'nullable|array',
            'is_main' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $data['is_main'] = $request->has('is_main');
        $data['is_active'] = $request->has('is_active');

        $store->update($data);

        return redirect()->route('stores.index')->with('success', 'Store updated successfully.');
    }

    public function destroy(\App\Models\Store $store)
    {
        $store->delete();
        return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
    }
}
