<?php

declare(strict_types=1);

namespace App\Admin\Controllers;

use Spatie\Activitylog\Models\Activity;

final class LogsActivityController
{
    public function index()
    {
        $activities = Activity::latest()->get();

        return view('admin.logs.index')->with(compact('activities'));
    }
}
