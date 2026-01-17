<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = \App\Models\Page::where('slug', '=', $slug)
            ->where('is_active', '=', true)
            ->firstOrFail();

        return view('frontend.pages.show', compact('page'));
    }
}
