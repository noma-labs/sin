<?php

namespace App\Rtn\Video;

use App\Traits\SortableTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static VideoQueryBuilder byYear()
 **/
class Video extends Model
{
    use SortableTrait;

    protected $connection = 'db_rtn';

    protected $table = 'arch_prof';

    protected $primaryKey = 'Id_regpro';

    public function newEloquentBuilder($query): VideoQueryBuilder
    {
        return new VideoQueryBuilder($query);
    }
}
