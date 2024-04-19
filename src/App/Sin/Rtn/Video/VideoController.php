<?php

namespace App\Rtn\Video;

use App\Core\Controllers\BaseController as CoreBaseController;
use Exception;
use Illuminate\Http\Request;

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
       dd($request->all());
    }
}
