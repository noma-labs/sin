<?php

namespace App\Nomadelfia\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;

class PopolazioneQueryBuilder extends Builder
{
    public function attuale(): self
    {
        return $this->whereNull('data_uscita');
    }

}