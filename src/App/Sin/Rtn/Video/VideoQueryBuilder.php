<?php

namespace App\Rtn\Video;

use Illuminate\Database\Eloquent\Builder;

class VideoQueryBuilder extends Builder
{
    public function byYear(): VideoQueryBuilder
    {
        return $this->selectRaw('YEAR(Datareg) as year, MONTHNAME(Datareg) as month, count(*) as count')
            ->groupByRaw('YEAR(Datareg), MONTH(Datareg)')
            ->orderBy('Datareg', 'desc');
    }
}
