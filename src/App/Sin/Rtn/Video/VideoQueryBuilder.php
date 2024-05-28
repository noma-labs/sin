<?php

namespace App\Rtn\Video;

use Illuminate\Contracts\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder;

class VideoQueryBuilder extends Builder
{
    public function byYear(): QueryBuilder
    {
        return $this->selectRaw('YEAR(Datareg) as year, MONTHNAME(Datareg) as month, count(*) as count')
            ->groupByRaw('YEAR(Datareg), MONTH(Datareg)')
            ->orderBy('Datareg', 'desc');
    }
}
