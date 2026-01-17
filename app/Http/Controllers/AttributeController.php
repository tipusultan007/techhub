<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index()
    {
        $attributes = Attribute::with('values')->latest()->paginate(10);
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created attribute in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name',
            'values' => 'required|array|min:1',
            'values.*' => 'required|string|distinct'
        ]);

        // 1. Create Attribute (e.g., Color)
        $attribute = Attribute::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        // 2. Create Values (e.g., Red, Blue)
        foreach ($request->values as $value) {
            if (!empty($value)) {
                $attribute->values()->create([
                    'value' => $value
                ]);
            }
        }

        return redirect()->route('attributes.index')->with('success', 'Attribute created successfully.');
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified attribute in storage.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
        ]);

        // 1. Update Name
        $attribute->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        // 2. Update Existing Values
        if ($request->has('existing_values')) {
            foreach ($request->existing_values as $id => $val) {
                AttributeValue::where('id', $id)->update(['value' => $val]);
            }
        }

        // 3. Add New Values
        if ($request->has('new_values')) {
            foreach ($request->new_values as $val) {
                if (!empty($val)) {
                    $attribute->values()->create(['value' => $val]);
                }
            }
        }

        return redirect()->route('attributes.index')->with('success', 'Attribute updated successfully.');
    }

    /**
     * Remove the specified attribute from storage.
     */
    public function destroy(Attribute $attribute)
    {
        // Note: Database cascade will delete values, but usually 
        // you can't delete if linked to a Product Variant (Foreign Key Constraint)
        try {
            $attribute->delete();
            return redirect()->route('attributes.index')->with('success', 'Attribute deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete attribute. It is likely being used by products.');
        }
    }

    /**
     * Remove a single value (AJAX or Form)
     */
    public function destroyValue($id)
    {
        try {
            AttributeValue::destroy($id);
            return back()->with('success', 'Value deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot delete this value. It is assigned to a product.');
        }
    }
}