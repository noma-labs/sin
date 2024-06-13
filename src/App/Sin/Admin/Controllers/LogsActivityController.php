<?php

namespace App\Admin\Controllers;

use Spatie\Activitylog\Models\Activity;

class LogsActivityController
{
    public function index()
    {
        $activities = Activity::latest()->get();

        return view('admin.logs.index')->with(['activities' => $activities]);
    }
}
