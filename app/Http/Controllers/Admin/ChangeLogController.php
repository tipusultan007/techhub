<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ChangeLogController extends Controller
{
    public function index()
    {
        $changelogs = app_changelog();
        return view('admin.changelog.index', compact('changelogs'));
    }
}
