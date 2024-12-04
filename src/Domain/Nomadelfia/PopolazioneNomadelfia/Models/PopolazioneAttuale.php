<?php

declare(strict_types=1);

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Models;

use Illuminate\Database\Eloquent\Model;

final class PopolazioneAttuale extends Model
{
    public $timestamps = true;

    protected $connection = 'db_nomadelfia';

    protected $table = 'v_popolazione_attuale';

    protected $guarded = [];
}
