<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log an activity.
     *
     * @param string $module The module name (e.g., POS, Quotation)
     * @param string $action The action performed (e.g., Create, Edit, Delete)
     * @param string $description A human-readable description
     * @param array|null $data Optional technical data for the log
     * @return void
     */
    public function logActivity($module, $action, $description, $data = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'data' => $data,
            'ip_address' => Request::ip(),
        ]);
    }
}
