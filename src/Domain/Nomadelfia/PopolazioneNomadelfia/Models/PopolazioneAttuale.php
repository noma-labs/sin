<?php

namespace Domain\Nomadelfia\PopolazioneNomadelfia\Models;

use Illuminate\Database\Eloquent\Model;

class PopolazioneAttuale extends Model
{
    protected $connection = 'db_nomadelfia';

    protected $table = 'v_popolazione_attuale';

    public $timestamps = true;

    protected $guarded = [];
}
