<?php

namespace App\Rtn\Video;

use App\Core\Controllers\BaseController as CoreBaseController;

class VideoController extends CoreBaseController
{
    public function create()
    {
        return view('rtn.video.create');
    }
}
