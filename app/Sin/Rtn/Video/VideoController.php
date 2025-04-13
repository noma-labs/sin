<?php

declare(strict_types=1);

namespace App\Rtn\Video;

use Exception;

final class VideoController
{
    public function index()
    {
        $countByYear = Video::byYear()->get();

        return view('rtn.video.create', compact('countByYear'));
    }

    public function create(): never
    {
        throw new Exception('not implemented yet');
    }

    public function store(): never
    {
        dd('Not implemented yet');
    }
}
