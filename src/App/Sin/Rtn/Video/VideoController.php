<?php

namespace App\Rtn\Video;

use App\Core\Controllers\BaseController as CoreBaseController;

class VideoController extends CoreBaseController
{
    public function create()
    {
        $countByYear = Video::byYear()->get();

        // all: [
        //     App\Rtn\Video\Video {#8004
        //       year: 2023,
        //       month: "August",
        //       count(*): 2,
        //     },
        //   ],
        return view('rtn.video.create', compact('countByYear'));
    }
}
