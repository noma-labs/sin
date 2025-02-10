<?php

declare(strict_types=1);

namespace App\Rtn\Video;

use Illuminate\Database\Eloquent\Builder;

final class VideoQueryBuilder extends Builder
{
    public function byYear(): self
    {
        return $this->selectRaw('YEAR(Datareg) as year, MONTHNAME(Datareg) as month, count(*) as count')
            ->groupByRaw('YEAR(Datareg), MONTH(Datareg)')
            ->orderBy('Datareg', 'desc');
    }
}
