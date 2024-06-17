<?php

namespace App\Rtn\Video;

use Exception;
use Illuminate\Http\Request;

class VideoController
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

    public function store(Request $request): void
    {
        dd('Not implemented yet');
    }
}
