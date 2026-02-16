<?php

namespace App\Http\Controllers;

use App\Models\AmcTemplate;
use Illuminate\Http\Request;

class AmcTemplateController extends Controller
{
    public function index()
    {
        $templates = AmcTemplate::latest()->paginate(10);
        return view('admin.amc-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.amc-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required',
        ]);

        if ($request->is_default) {
            AmcTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        AmcTemplate::create($request->all());

        return redirect()->route('amc-templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(AmcTemplate $amcTemplate)
    {
        return view('admin.amc-templates.edit', compact('amcTemplate'));
    }

    public function update(Request $request, AmcTemplate $amcTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required',
        ]);

        if ($request->is_default) {
            AmcTemplate::where('id', '!=', $amcTemplate->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $amcTemplate->update($request->all());

        return redirect()->route('amc-templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(AmcTemplate $amcTemplate)
    {
        $amcTemplate->delete();
        return redirect()->route('amc-templates.index')->with('success', 'Template deleted successfully.');
    }
}
