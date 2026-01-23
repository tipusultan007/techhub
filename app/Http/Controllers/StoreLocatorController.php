<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreLocatorController extends Controller
{
    /**
     * Display the Store Locator page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('frontend.store-locator');
    }
}
