<?php

declare(strict_types=1);

namespace App\Rtn\Video;

use Exception;
use Illuminate\Http\Request;

final class VideoController
{
    public function index()
    {
        $countByYear = Video::byYear()->get();

        return view('rtn.video.create', compact('countByYear'));
    }

    public function create(): void
    {
        throw new Exception('not implemented yet');
    }

    public function store(): void
    {
        dd('Not implemented yet');
    }
}
