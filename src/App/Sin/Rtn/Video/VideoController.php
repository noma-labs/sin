<?php

namespace App\Rtn\Video;

use App\Core\Controllers\BaseController as CoreBaseController;
use Composer\DependencyResolver\Request;
use Exception;

class VideoController extends CoreBaseController
{
    public function index()
    {
        $countByYear = Video::byYear()->get();

        return view('rtn.video.create', compact('countByYear'));
    }

    public function create()
    {
        throw new Exception('not implemented yet');
    }

    public function store(Request $request)
    {
        dd("DSAD");
       dd($request->all());
    }
}
