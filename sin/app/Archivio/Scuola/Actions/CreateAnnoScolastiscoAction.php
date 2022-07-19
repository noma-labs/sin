<?php

namespace App\Scuola\Models;

use App\Nomadelfia\Models\Persona;
use Carbon\Carbon;

class CreateAnnoScolastiscoAction
{

    public function execute(
        Anno $copy_from=null
    ): Anno {
        $a = Anno::getAnnoScolastico($as);

        return $a;
    }

}