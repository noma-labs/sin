<?php
namespace App\Admin\Controllers;

use App\Core\Controllers\BaseController as Controller;

use Spatie\Activitylog\Models\Activity;

class LogsActivityController extends Controller
{
    public function __construct() {
        // $this->middleware(['auth', 'isAdmin','isMaster']); //isAdmin middleware lets only users with the admin role
    }

    public function index()
    {
        $activities = Activity::latest()->get();
        return view("admin.logs.index")->with(compact('activities'));
    }
}
