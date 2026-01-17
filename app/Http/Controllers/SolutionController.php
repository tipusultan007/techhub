<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function index()
    {
        $solutions = \App\Models\Solution::where('is_active', 1)
            ->orderBy('order', 'asc')
            ->get();

        return view('frontend.solutions.index', compact('solutions'));
    }

    public function show($slug)
    {
        $solution = \App\Models\Solution::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        return view('frontend.solutions.show', compact('solution'));
    }
}
