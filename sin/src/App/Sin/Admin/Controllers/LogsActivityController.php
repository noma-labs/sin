<?php

namespace App\Admin\Controllers;

use App\Core\Controllers\BaseController as Controller;

use Spatie\Activitylog\Models\Activity;

class LogsActivityController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $activities = Activity::latest()->get();
        return view("admin.logs.index")->with(compact('activities'));
    }
}
